# scripts/keel-tools/claude.sh — Keel tool row. SOURCED, never executed.
#
# The ONLY file in this project that knows anything about Claude Code. The
# shared scripts (keel-continue, keel-close, keel-stop-hook, keel-chain-check,
# keel-handoff-verify, keel-session-pid.sh) resolve the detected tool's row,
# source it and use the fields; none of them carries this tool's name on an
# executable line. Contract: Keel's references/project-state.md, "The registry
# is DATA".
#
# Editing this file un-proves the chain exactly as editing scripts/keel-continue
# does: the card's `Chain verified:` line carries this file's checksum, so run
# `scripts/keel-chain-check --smoke` after any change here.
#
# Only a session running IN Claude Code may edit this row.

# --- The contract's fields --------------------------------------------------
KEEL_TOOL_NAME="claude"
# VERIFIED | DOCUMENTED | NONE — only VERIFIED ever fires. The `start` action
# was observed end to end on this machine under D-015 (2026-08-01), with the
# launcher this project carried then. Whether the CURRENT launcher and row have
# been observed is a different question, answered by the card's
# `Chain verified:` line and nothing else.
KEEL_TOOL_EVIDENCE="VERIFIED"
KEEL_TOOL_VERIFIED_ON="2026-08-01"
# The tier this row's action reaches. Two surfaces, one discriminator: the
# VS Code extension reports the entrypoint `claude-vscode` and its URI handler
# pre-fills and never submits; EVERY other entrypoint is the CLI, which
# submits. The CLI's values are never enumerated — the vendor extends them.
KEEL_TOOL_TIER="start"
[ "${CLAUDE_CODE_ENTRYPOINT:-}" = "claude-vscode" ] && KEEL_TOOL_TIER="prefill"
# The command that must be on PATH, re-checked live before every fire.
KEEL_TOOL_CLI="claude"
# May the card's `Chaining model:` be passed to this tool?
KEEL_TOOL_PASS_MODEL="yes"
# Is scripts/keel-stop-hook's output schema THIS tool's own contract?
KEEL_TOOL_STOP_HOOK="yes"
# Where THIS tool registers turn-end hooks.
KEEL_TOOL_HOOK_FILE=".claude/settings.json"
# Command answering "is another session live in <dir>": the directory is
# appended as the last argument, and the answer is a JSON list of objects
# carrying at least `pid` and `cwd`.
KEEL_TOOL_LIVE_QUERY="claude agents --json --cwd"

# Echoes this tool's name when it recognises its OWN marker, nothing otherwise.
keel_tool_detect() {
	[ "${CLAUDECODE:-}" = "1" ] && printf '%s\n' "$KEEL_TOOL_NAME"
	return 0
}

# --- Row extensions ---------------------------------------------------------
# Not in the contract's field list; they exist because each is a fact a shared
# script would otherwise have to hardcode about THIS tool. A shared script
# calls one only when the row defines it.

# What the `start` action needs besides KEEL_TOOL_CLI. Prints the missing
# capability and returns 1, or returns 0 silently.
keel_tool_preflight() {
	[ "$KEEL_TOOL_TIER" = "start" ] || return 0
	[ -n "${KEEL_TEST_OPENER:-}" ] && return 0
	if [ "$(uname -s 2>/dev/null)" != "Darwin" ]; then
		printf '%s\n' "the 'start' action is verified on macOS only (this is $(uname -s 2>/dev/null))"
		return 1
	fi
	if ! command -v osascript >/dev/null 2>&1; then
		printf '%s\n' "'osascript' is not available, so no visible Terminal session can be opened"
		return 1
	fi
	return 0
}

# $1 repo root, $2 prompt, $3 model (may be empty) — performs this tool's own
# launch, ONCE. Never retried by anyone.
#
# start: the command is written to a real executable script file and Terminal
# is asked to run that file BY PATH. Nothing is interpolated into an
# AppleScript string: the path travels as an argument (`item 1 of argv`), so
# there is no nested quoting in any layer (contract point 6a). The mktemp
# template ends in its X run, with no suffix (BSD mktemp does not substitute
# a template that carries one). The prompt is a short instruction to READ the
# hand-off, never the hand-off's content (point 6b).
#
# With KEEL_SMOKE_NONCE set (scripts/keel-chain-check --smoke), the script
# file prints a marker instead of starting a session, so the launch path is
# proven without opening an unattended chat.
#
# KEEL_TEST_OPENER, when set, is run with the script path instead of the
# Terminal action. It exists for stub tests and for nothing else.
keel_tool_launch() {
	local _root="$1" _prompt="$2" _model="${3:-}" _script _tmp _enc

	if [ "$KEEL_TOOL_TIER" = "prefill" ]; then
		_enc=$(python3 -c 'import sys, urllib.parse; print(urllib.parse.quote(sys.argv[1], safe=""))' "$_prompt" 2>/dev/null)
		[ -n "$_enc" ] || return 1
		if [ -n "${KEEL_TEST_OPENER:-}" ]; then
			"$KEEL_TEST_OPENER" "vscode://anthropic.claude-code/open?prompt=${_enc}"
			return $?
		fi
		command -v open >/dev/null 2>&1 || return 1
		open "vscode://anthropic.claude-code/open?prompt=${_enc}"
		return $?
	fi

	_tmp="${TMPDIR:-/tmp}"
	_tmp="${_tmp%/}"
	_script=$(mktemp "$_tmp/keel-continue-launch.XXXXXX") || return 1
	{
		printf '#!/bin/bash\n'
		printf 'rm -f "$0"\n'
		printf 'cd %q || { echo "keel launch: cannot enter the repository"; exit 1; }\n' "$_root"
		if [ -n "${KEEL_SMOKE_NONCE:-}" ]; then
			printf 'tty >%q 2>/dev/null\n' "${KEEL_SMOKE_DIR:-/tmp}/tty"
			printf 'echo run >>%q\n' "${KEEL_SMOKE_DIR:-/tmp}/runs"
			printf 'printf "KEEL-SMOKE-OK:%%s cwd=%%s cli=%%s\\n" %q "$PWD" "$(command -v %q || echo MISSING)"\n' "$KEEL_SMOKE_NONCE" "$KEEL_TOOL_CLI"
		elif [ -n "$_model" ]; then
			printf 'exec %q --model %q %q\n' "$KEEL_TOOL_CLI" "$_model" "$_prompt"
		else
			printf 'exec %q %q\n' "$KEEL_TOOL_CLI" "$_prompt"
		fi
	} >"$_script"
	chmod +x "$_script" || return 1
	# The string handed to Terminal must be a bare path and nothing else.
	case "$_script" in
	*[!A-Za-z0-9_./-]*)
		rm -f "$_script"
		printf '%s\n' "launch script path is not a bare path: refusing to hand it to Terminal"
		return 1
		;;
	esac
	printf 'Launch script (executed by path): %s\n' "$_script"
	sed 's/^/    | /' "$_script"

	if [ -n "${KEEL_TEST_OPENER:-}" ]; then
		"$KEEL_TEST_OPENER" "$_script"
		return $?
	fi
	osascript \
		-e 'on run argv' \
		-e 'tell application "Terminal"' \
		-e 'activate' \
		-e 'do script (item 1 of argv)' \
		-e 'end tell' \
		-e 'end run' \
		"$_script" >/dev/null 2>&1
}

# --- Smoke read-back (used only by scripts/keel-chain-check --smoke) --------
# With KEEL_TEST_OPENER set these read the stub's own files:
#   <opener>.log  one line per call        <opener>.out  what the script printed

# Prints how many terminal windows are open.
keel_tool_smoke_windows() {
	if [ -n "${KEEL_TEST_OPENER:-}" ]; then
		if [ -f "${KEEL_TEST_OPENER}.log" ]; then
			grep -c . "${KEEL_TEST_OPENER}.log"
		else
			echo 0
		fi
		return 0
	fi
	osascript -e 'tell application "Terminal" to count windows' 2>/dev/null
}

# $1 tty — prints the real history of the Terminal tab on that tty.
keel_tool_smoke_readback() {
	if [ -n "${KEEL_TEST_OPENER:-}" ]; then
		[ -f "${KEEL_TEST_OPENER}.out" ] && cat "${KEEL_TEST_OPENER}.out"
		return 0
	fi
	osascript \
		-e 'on run argv' \
		-e 'set wanted to item 1 of argv' \
		-e 'tell application "Terminal"' \
		-e 'repeat with w in windows' \
		-e 'repeat with t in tabs of w' \
		-e 'if (tty of t) is wanted then return (history of t)' \
		-e 'end repeat' \
		-e 'end repeat' \
		-e 'end tell' \
		-e 'return ""' \
		-e 'end run' \
		"$1" 2>/dev/null
}

# $1 tty — closes the window the smoke test opened.
keel_tool_smoke_close() {
	if [ -n "${KEEL_TEST_OPENER:-}" ]; then
		echo "closed (stub)"
		return 0
	fi
	osascript \
		-e 'on run argv' \
		-e 'set wanted to item 1 of argv' \
		-e 'tell application "Terminal"' \
		-e 'repeat with w in windows' \
		-e 'repeat with t in tabs of w' \
		-e 'if (tty of t) is wanted then' \
		-e 'close w' \
		-e 'return "closed"' \
		-e 'end if' \
		-e 'end repeat' \
		-e 'end repeat' \
		-e 'end tell' \
		-e 'return "not found"' \
		-e 'end run' \
		"$1" 2>/dev/null
}

# --- Permission container (used only by scripts/keel-chain-check) -----------
# This tool keeps a committed container (.claude/settings.json) and a
# machine-local one (.claude/settings.local.json). An allow-list entry in
# either is honoured; the permission mode and env.PATH are machine-local.

# $1 repo root, $2 script name (e.g. keel-continue). Prints where the entry
# was found; returns 1 when it is in neither container.
keel_tool_allow_listed() {
	python3 - "$1" "$2" <<'KEEL_PY'
import json, os, sys
root, name = sys.argv[1], sys.argv[2]
want = "Bash(./scripts/%s:*)" % name
containers = (".claude/settings.json", ".claude/settings.local.json")
found = []
for rel in containers:
    try:
        with open(os.path.join(root, rel)) as handle:
            data = json.load(handle)
    except Exception:
        continue
    allow = (data.get("permissions") or {}).get("allow") or []
    if want in allow:
        found.append(rel)
if found:
    print("%s present in %s" % (want, ", ".join(found)))
    sys.exit(0)
print("%s missing from %s" % (want, " and ".join(containers)))
sys.exit(1)
KEEL_PY
}

# $1 repo root. Prints permissions.defaultMode from the machine-local
# container (empty when unset or unreadable).
keel_tool_permission_mode() {
	python3 - "$1" <<'KEEL_PY'
import json, os, sys
try:
    with open(os.path.join(sys.argv[1], ".claude/settings.local.json")) as handle:
        data = json.load(handle)
    print((data.get("permissions") or {}).get("defaultMode") or "")
except Exception:
    print("")
KEEL_PY
}

# $1 repo root. Prints env.PATH as declared in the machine-local container
# (empty when unset or unreadable).
keel_tool_declared_path() {
	python3 - "$1" <<'KEEL_PY'
import json, os, sys
try:
    with open(os.path.join(sys.argv[1], ".claude/settings.local.json")) as handle:
        data = json.load(handle)
    print((data.get("env") or {}).get("PATH") or "")
except Exception:
    print("")
KEEL_PY
}

# Names the files the three functions above read, for messages.
KEEL_TOOL_PERMISSION_FILES=".claude/settings.json (committed) / .claude/settings.local.json (machine-local)"
