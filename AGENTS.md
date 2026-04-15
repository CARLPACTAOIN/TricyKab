# AGENTS.md

## Source of Truth

- `PRD_MD_V2.md` is the authoritative product and implementation source of truth for this repository.
- `PRD_MD_V2.md` governs feature scope, business rules, architecture, flows, state behavior, API intent, and acceptance criteria.
- `PRD_PROGRESS_AUDIT.md` is the authoritative implementation-status tracker for the current branch against the PRD.
- `PRD_PROGRESS_AUDIT.md` does not replace, weaken, or override `PRD_MD_V2.md`.
- If the current codebase and `PRD_MD_V2.md` disagree, the PRD wins by default.

## PRD-First Workflow

- Before planning, modifying, or implementing work, read the relevant sections of `PRD_MD_V2.md`.
- When changing an existing feature, review both the relevant PRD section and the corresponding entry in `PRD_PROGRESS_AUDIT.md`.
- Use PRD section headings when explaining why a change is being made.
- Do not treat placeholders, static demo data, navigation stubs, or partial CRUD as completed PRD delivery unless the actual PRD behavior is implemented.
- Do not infer completed scope from UI presence alone; confirm the underlying behavior against the PRD.

## Conflict Handling

- If a user request conflicts with `PRD_MD_V2.md`, do not implement it immediately.
- Call out the specific conflict and require an explicit override before proceeding.
- If an explicit override is given, treat the work as a PRD deviation rather than normal implementation.
- After implementing an approved deviation, record it in `PRD_PROGRESS_AUDIT.md` as off-PRD, temporary scope, or revised scope as appropriate.

## Progress Audit Maintenance

- After any repo-tracked change, review `PRD_PROGRESS_AUDIT.md`.
- Update `PRD_PROGRESS_AUDIT.md` only when the change materially affects implementation status, gaps, evidence, sprint notes, or off-PRD deviations.
- Preserve the audit's evidence-based style and existing status legend.
- Update only the impacted entries rather than rewriting unrelated sections.
- If progress did not materially change, no audit edit is required after review.
- If progress did materially change, the audit update is part of the same task and is not optional.

## UI/UX Skill Requirement

- For any UI/UX design, implementation, refinement, or review task, consult the local installed skill at `.agent/skills/.agent/skills/ui-ux-pro-max/SKILL.md` before making design decisions.
- Use that skill before choosing layout direction, visual style, typography, color systems, interaction patterns, or UX recommendations.
- Treat `.agent/skills/ui-ux-pro-max-skill/` as the upstream bundled source, not the primary project-facing instruction path.
- Keep UI work aligned with `PRD_MD_V2.md` and the current project direction.
- The UI/UX skill improves design quality and decision support, but it does not override product scope or PRD requirements.

## Practical Rules

- Reference `PRD_MD_V2.md`, `PRD_PROGRESS_AUDIT.md`, and `.agent/skills/.agent/skills/ui-ux-pro-max/SKILL.md` directly when they are relevant to the task.
- Prefer correcting implementation toward the PRD over preserving legacy behavior that predates the PRD.
- If a task changes project progress in a meaningful way, update the audit in the same task before considering the work complete.
