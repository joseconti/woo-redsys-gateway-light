<!-- KEEL:BEGIN — v5.9.0 do not remove: binds every AI/session in this repo to the Keel workflow -->
# Keel protocol (mandatory for ANY assistant working in this repository)

This project is governed by the Keel workflow. Before reading code or changing ANYTHING:

1. Read the FULL Keel `SKILL.md` FIRST, before anything else in this repository —
   from the installed `keel` skill if present, otherwise from the embedded copy
   at `.claude/skills/keel/SKILL.md` or `.agents/skills/keel/SKILL.md` — and
   follow it literally, starting by
   executing its maintenance block (`references/keel-maintenance.md` — update
   check, lock freshness). Remembering the protocol from an earlier chat, or
   having this lock in context, does NOT count as having read it: a session that
   works without having read SKILL.md in this session is out of protocol. If the
   update check installs a newer Keel, re-read the new `SKILL.md` and run its
   post-update reconciliation (defined in Keel's `references/project-state.md`)
   BEFORE normal work continues, so this project is brought up to date with
   everything the new version requires — new files or directories, new
   project-card lines, this very lock block, questions never asked here.
2. Then check `docs/continuation-prompt.md` BEFORE the state files — it is the
   freshest pointer in this project, and it is what lets a bare "continue" start
   the work with no recap. If it exists, run `scripts/keel-handoff-verify` and
   obey the verdict: CONTINUE starts at the position it names; STOP discards it
   as a courier and you resume from the committed state instead, saying so in
   one line — a stale or foreign hand-off loses its authority, it never stops
   the session. Absent is ordinary (it is gitignored, so a fresh clone has
   none). It is a courier and never an authority: where it and the committed
   state disagree, `docs/PROGRESS.md` wins. Then read
   `docs/PROGRESS.md` (project card, current position, next action),
   `docs/decisions.md` (decisions are NEVER re-opened on your initiative), and
   `docs/lessons-learned.md` (recorded mistakes are never repeated), plus the
   phase reference SKILL.md names for the current phase. If the project card's
   `Keel baseline:` is older than the running Keel (or missing), offer the
   post-update reconciliation before continuing.
3. Follow the recorded specs and design exactly: no reinterpretation, no silent
   deviation, no "improving" recorded decisions. Anything undefined → ask the user.
   Design gaps → Design Request (Keel Phase 4). Never claim something that was not
   verified: the code map in `docs/03-technical-plan.md` is a TARGET tree, so a path
   not marked `[E]` is absent until a slice creates it, and a control, check or test
   is only described in the present tense once it is built and evidenced. A check
   whose inputs do not exist yet is "not yet applicable", naming what is missing —
   never "passed". Before changing anything, read the change map's row for that type
   of change: it lists every artifact that must be touched.
4. Update `docs/PROGRESS.md` and `docs/decisions.md` at the moment of every change.
   Commit at passed test points — never without first checking the staged files for
   confidential data (secrets, credentials, private keys, tokens, real personal or
   customer data). A finding STOPS the commit: warn the user file by file that
   pushing it is a serious security risk, and exclude it via `.gitignore` (already
   tracked: untrack it too; ever pushed: purge history AND rotate the credential)
   before committing anything. NEVER leave work uncommitted at the end of a block:
   commit to `develop` or to a work branch bound for it, and where this repository
   has only `main`/`master`, create `develop` first. If it has NO REMOTE, say so and
   offer to publish it — a local commit survives a bad edit, not a dead disk, and
   work that exists only on one machine is one accident from not existing at all.
5. NEVER end a session mid-work — and NEVER close a sprint, even if you carry on
   working — leaving the user with nothing current to continue from
   (UNBREAKABLE). A sprint close is where a person walks away, so the hand-off
   must already be on disk and CURRENT at that moment, whatever the autonomy or
   chaining settings say; if you keep working past it, rewrite the file as the
   work advances, because a hand-off pointing at an old commit fails its own
   verification and is worse than none. Produce the continuation prompt from the embedded skill's
   `references/project-state.md`, SHOW it in the conversation ready to copy, and
   WRITE it to `docs/continuation-prompt.md` with its freshness header
   (`Repo` / `Generated` / `Keel` / `Commit` / `Tree` / `Position` / `Handover`). Running low on
   context is when this is most likely to be skipped and most expensive to skip:
   do it BEFORE the session is exhausted, never as an afterthought. Reading one of
   those files obliges the reverse duty — check that its real path is INSIDE this
   session's `git rev-parse --show-toplevel`, then its `Repo`, `Commit`, `Tree` and
   timestamp against the repository you are actually in, and STOP rather than act on
   a stale hand-off OR on another checkout's: the filename is the same in every Keel
   project, and a worktree or second clone shares both repository and commit, so
   containment is the only check that separates them. Where the project card's `Chaining:` allows it and the hand-off
   is clean, and the tool you are running in has a VERIFIED action recorded, also
   chain the next chat — passing this repository's ABSOLUTE hand-off path — then
   close this one in one short message in the CONVERSATION's language. If a chat
   cannot be opened for ANY reason — including "this tool has no recorded action",
   which is the normal case — that is not an error to report: print the prompt to
   be copied. The FILE works in every tool and needs no integration; only the
   auto-open is tool-specific, and it is never guessed.
6. Work with execution discipline, whatever model or environment is running:
   - Batch independent tool calls in ONE parallel block; never run sequentially what
     does not depend on a previous result.
   - Delegate broad searches/scans to a subagent when the environment provides them;
     bring back conclusions, never file dumps — the main context stays clean.
   - The same batching rule governs delegation: independent READING verifiers at one
     gate, and one agent per independent unit (screen, locale, competitor), go out in
     ONE parallel block — with at most one EXECUTING verifier per environment running
     alongside them — and the gate is judged against their merged findings. Serial
     only when one check's input is another's output, when two executing agents would
     share one environment (playground, test machine, database, deployed origin), when
     an agent in the set can write (that one runs first, alone), or when concurrency
     is capped.
   - Do not narrate between tool calls ("now I will…"); accumulate findings and
     report once, at the end of the work block.
   - Locate before reading: search/grep first, then read only the relevant fragment.
     Never read whole files or directories "for context".
   - Edit surgically (exact-match edits on the changed lines); never rewrite a whole
     file to change one part.
   - Batch clarifying questions at the START of a work block; close every work block
     with an explicit verification step (diff, test, or re-read) before calling it
     done.

This block itself can be outdated: the version stamp on the `KEEL:BEGIN`
delimiter names the Keel that last wrote it. If that stamp differs from the
running Keel version (or is missing), refresh this whole block from the
canonical copy in Keel's `references/project-state.md` ("Portability") —
between the delimiters only, with the user's OK, restamped with the running
version. The stamp alone decides; no content comparison is needed.

If neither the skill nor the embedded copy is available: STOP and tell the user to
install Keel (or restore the embedded copy at `.claude/skills/keel/` /
`.agents/skills/keel/`) before continuing.
<!-- KEEL:END -->
