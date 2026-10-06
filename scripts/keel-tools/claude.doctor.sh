# scripts/keel-tools/claude.doctor.sh — Claude Code facts for the environment
# doctor and the test selector. SOURCED, never executed.
#
# This is NOT the tool row. The row is scripts/keel-tools/claude.sh, whose
# checksum is stamped on the project card (`Chain verified:`); this file is a
# separate companion so that adding a doctor fact never un-proves the chain.
# It holds what scripts/keel-doctor and scripts/keel-affected-tests would
# otherwise have to hardcode about this assistant, and that the row's own
# fields and functions (KEEL_TOOL_HOOK_FILE, KEEL_TOOL_PERMISSION_FILES,
# keel_tool_permission_mode, ...) do not already declare.
#
# Rules: variables only, every name prefixed KEEL_TOOL_DOCTOR_. No function,
# and none of the row's own KEEL_TOOL_* fields — the chaining scripts source
# every *.sh in this directory while looking for a row, and a file that sets
# neither keel_tool_detect nor KEEL_TOOL_EVIDENCE is skipped by all of them.
# scripts/keel-verify keys its registry checks on <tool>.sh and ignores
# <tool>.doctor.sh.

# This assistant's configuration directory inside the project. A diff that
# touches only files under it reaches no test (the selector's docs-only list).
KEEL_TOOL_DOCTOR_PROJECT_DIR=".claude"

# What to tell the person when the permission mode read through the row's
# keel_tool_permission_mode is manual or unset.
KEEL_TOOL_DOCTOR_PERMISSION_FIX='write .claude/settings.local.json (permissions.defaultMode: "auto"), or start with --permission-mode auto'

# User-level containers, relative to $HOME, where an MCP server registered
# for every session on the machine can live:
#   the file whose top-level `mcpServers` is user scope;
KEEL_TOOL_DOCTOR_USER_MCP_FILE=".claude.json"
#   the user settings file whose `enabledPlugins` lists plugins switched on;
KEEL_TOOL_DOCTOR_USER_SETTINGS_FILE=".claude/settings.json"
#   the directory under which those plugins keep their bundled `.mcp.json`.
KEEL_TOOL_DOCTOR_USER_PLUGINS_DIR=".claude/plugins"

# The command that removes a user-level registration of the browser MCP.
KEEL_TOOL_DOCTOR_MCP_REMOVE="claude mcp remove -s user playwright"
