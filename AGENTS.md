# Token-Optimized Developer Protocol
Model Target: Claude 3.5 Sonnet

## 1. Global Token Constraints (Apply to ALL Tasks)
*   **Zero Conversational Filler:** Omit all greetings, apologies, affirmations ("I understand"), and concluding remarks ("Let me know if you need anything else").
*   **Minimal Output:** Never explain code or summarize actions unless explicitly requested. Output only the solution.
*   **Strict Diff Formatting:** Never output an entire file unless creating a new one. Always use `// ... existing code ...` to skip unchanged sections.
*   **Targeted Discovery:** Never read or list entire directories. Use precise file paths or search commands to locate specific widgets, API endpoints, or functions.
*   **Tech Stack Assumptions:** Assume expert knowledge of standard Flutter and Dart patterns. Do not write boilerplate for external state management frameworks; stick to native/built-in state handling.

## 2. [LOW] Mode: Documentation & Small Fixes
**Trigger:** The user will prefix their prompt with `[LOW]`.
**Execution Rules:**
*   **Scope:** Read ONLY the specific file or lines mentioned in the prompt. Do not trace imports or read related files.
*   **Action:** Provide only the exact lines to replace, the typo fix, or the exact docstring to insert. 
*   **Verification:** Do not run formatting, build commands, or tests unless explicitly instructed.

## 3. [MEDIUM] Mode: Coding, Debugging & Features
**Trigger:** The user will prefix their prompt with `[MEDIUM]`.
**Execution Rules:**
*   **Context Gathering:** Limit file reads to the immediate class/widget and its direct data model. Stop reading after 3 files unless authorized.
*   **Debugging:** Provide a maximum 1-sentence root cause analysis before outputting the code fix. Do not explain standard Flutter error logs.
*   **Feature Addition:** Write code incrementally. Write the core logic first, then ask the user before spending tokens on extensive UI styling, error handling, or edge cases.

## 4. Terminal & Build Error Guardrails (Anti-Loop System)
**Objective:** Prevent runaway context window consumption during failed builds or test runs.

*   **The One-Strike Rule:** If a terminal command (e.g., `flutter run`, `flutter test`) fails, you are permitted a maximum of **ONE** autonomous attempt to apply a fix and retry. If it fails a second time, you MUST halt execution and ask the user how to proceed.
*   **Log Truncation:** Do not ingest massive build logs. If a command outputs a massive stack trace, analyze only the first 20 lines of the trace and the final error message. 
*   **No Blind Sweeps:** You are strictly prohibited from autonomously running `flutter clean`, `rm -rf ios/Pods`, or full system rebuilds. These commands generate massive console output that burns inbound tokens.
*   **Halt on Cascading Errors:** If fixing a single compiler error generates three or more new errors in different files, stop immediately. Output a 1-sentence summary of the cascade and wait for human authorization before modifying any further files.
*   **Banned Autonomous Commands:** Never run `pod install`, `./gradlew clean`, or update global dependencies without explicit human permission.

## 5. [HIGH] Mode: Architecture, Refactoring & Security Sweeps
**Trigger:** The user will prefix their prompt with `[HIGH]`.
**Objective:** Prevent runaway credit usage during massive codebase changes by enforcing a strict "Plan First, Code Second" protocol.

**Execution Rules:**
*   **Phase 1: Discovery (No Code Generation):** Read the necessary files to understand the data flow (e.g., tracing the `IntegrationController` to the frontend gate pages). Stop when you have enough context.
*   **Phase 2: The Execution Plan:** Output a strict, bulleted step-by-step plan of attack. Identify exactly which files will be modified, what security rules will be applied (e.g., escaping XSS in Blade/React, moving secrets to environment variables), and potential breaking changes. 
*   **Phase 3: Halt and Wait:** DO NOT write the code yet. End your response and wait for the user to approve or modify the plan.
*   **Phase 4: Incremental Delivery:** Once approved, execute the plan strictly one step at a time. Output the code for step 1, then stop and wait for the user to say "Next" or report errors before moving to the subsequent file.
*   **Scope Discipline:** Fix only the scope defined in the plan. Do not "clean up" adjacent code or apply styling unless it directly impacts the current security or architecture goal.