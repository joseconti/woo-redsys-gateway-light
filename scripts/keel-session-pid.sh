# scripts/keel-session-pid.sh — one answer to "who am I". SOURCED, never executed.
#
# Exposes ONE function, keel_session_pid, per Keel's references/project-state.md
# ("scripts/keel-session-pid.sh"). Every mechanism that records or compares a
# session — the single lane's owner, the launch receipt's launcher-pid, the
# baton, the per-session fire ledger, the close-out record, the stop hook's
# block log and its concurrency probe — sources this file and calls it, and
# never re-derives the answer inline.
#
# The identity is "<pid>-<start>": the PID of the long-lived assistant process
# that owns this session, plus that process's start time (letters and digits
# only), so a reused PID number cannot impersonate a dead session. The bare PID
# is the part before the first "-":  pid="${id%%-*}"
#
# How the owning process is found: this code runs in a transient subprocess
# (a tool shell, a hook, a script), so it walks UP the process tree from its
# parent looking for the session's own process. Which process name that is, is
# a per-tool fact, so it is read from the tool rows (scripts/keel-tools/*.sh,
# field KEEL_TOOL_CLI) and never written here. Measured on the macOS CLI: the
# walk is two hops (tool shell -> session process).
#
# Return status: 0 when the owning session process was found; 1 when it was
# not (no row matched within 12 hops) — the function then still prints an
# identity, built from the immediate parent, so callers keep working, but that
# identity is transient and callers that need a stable one must treat status 1
# as "not established".
#
# KEEL_TEST_SESSION_ID overrides the derivation. It exists for fixtures that
# have to describe two sessions in one process tree, and for nothing else.

keel_session_pid() {
	if [ -n "${KEEL_TEST_SESSION_ID:-}" ]; then
		printf '%s\n' "$KEEL_TEST_SESSION_ID"
		return 0
	fi
	if [ -n "${_KEEL_SESSION_ID:-}" ]; then
		printf '%s\n' "$_KEEL_SESSION_ID"
		return "${_KEEL_SESSION_RC:-0}"
	fi

	local _ksp_dir _ksp_row _ksp_name _ksp_names _ksp_p _ksp_hops _ksp_comm
	local _ksp_base _ksp_parent _ksp_found _ksp_start _ksp_n

	_ksp_dir="${KEEL_TOOLS_DIR:-}"
	if [ -z "$_ksp_dir" ]; then
		_ksp_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" 2>/dev/null && pwd)/keel-tools"
	fi

	# The process names worth recognising: every row's KEEL_TOOL_CLI. Each row
	# is sourced in a subshell so its fields and functions never leak into the
	# caller.
	_ksp_names=""
	for _ksp_row in "$_ksp_dir"/*.sh; do
		[ -f "$_ksp_row" ] || continue
		_ksp_name=$( (
			. "$_ksp_row" >/dev/null 2>&1
			printf '%s' "${KEEL_TOOL_CLI:-}"
		) 2>/dev/null)
		[ -n "$_ksp_name" ] && _ksp_names="$_ksp_names $_ksp_name"
	done

	_ksp_found=""
	_ksp_p="$PPID"
	_ksp_hops=0
	while [ -n "$_ksp_p" ] && [ "$_ksp_p" != "1" ] && [ "$_ksp_p" != "0" ] && [ "$_ksp_hops" -lt 12 ]; do
		_ksp_comm=$(ps -o comm= -p "$_ksp_p" 2>/dev/null | sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//')
		_ksp_base="${_ksp_comm##*/}"
		_ksp_base="${_ksp_base#-}"
		for _ksp_n in $_ksp_names; do
			if [ "$_ksp_base" = "$_ksp_n" ]; then
				_ksp_found="$_ksp_p"
				break
			fi
			# A per-user installer may run the binary from a versioned path
			# whose basename is a version number: match the directory instead.
			case "$_ksp_comm" in
			*/"$_ksp_n"/*)
				_ksp_found="$_ksp_p"
				break
				;;
			esac
		done
		[ -n "$_ksp_found" ] && break
		_ksp_parent=$(ps -o ppid= -p "$_ksp_p" 2>/dev/null | tr -d ' ')
		[ -z "$_ksp_parent" ] && break
		_ksp_p="$_ksp_parent"
		_ksp_hops=$((_ksp_hops + 1))
	done

	_KEEL_SESSION_RC=0
	if [ -z "$_ksp_found" ]; then
		_ksp_found="$PPID"
		_KEEL_SESSION_RC=1
	fi
	_ksp_start=$(LC_ALL=C ps -o lstart= -p "$_ksp_found" 2>/dev/null | tr -cd 'A-Za-z0-9')
	[ -z "$_ksp_start" ] && _ksp_start="unknown"
	_KEEL_SESSION_ID="${_ksp_found}-${_ksp_start}"
	printf '%s\n' "$_KEEL_SESSION_ID"
	return "$_KEEL_SESSION_RC"
}
