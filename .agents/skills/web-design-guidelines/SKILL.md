---
name: web-design-guidelines
description: Review UI code for Web Interface Guidelines compliance. Use when asked to "review my UI", "check accessibility", "audit design", "review UX", or "check my site against best practices".
metadata:
  author: vercel
  version: "1.0.0"
  argument-hint: <file-or-pattern>
---

# Web Interface Guidelines

Review files for compliance with Web Interface Guidelines.

## How It Works

1. Fetch the latest guidelines from the source URL below
2. Read the specified files (or prompt user for files/pattern)
3. Check against all rules in the fetched guidelines
4. Output findings in the terse `file:line` format

## Guidelines Source

Fetch fresh guidelines before each review:

```
https://raw.githubusercontent.com/vercel-labs/web-interface-guidelines/main/command.md
```

Use WebFetch to retrieve the latest rules. The fetched content contains all the rules and output format instructions.

## Usage

When a user provides a file or pattern argument:
1. Fetch guidelines from the source URL above
2. Read the specified files
3. Apply all rules from the fetched guidelines
4. Output findings using the format specified in the guidelines

If no files specified, ask the user which files to review.

## Review Constraints

> [!IMPORTANT]
> **Strictly Grounded Findings**: Do NOT invent violations or report subjective preferences. Only report an issue when it directly maps to an explicit rule found in the fetched guideline document. If a component adheres to the guidelines or falls outside their scope, do not invent artificial findings.

> [!WARNING]
> **Context Translation (CodeIgniter 3 vs React)**: The fetched guidelines were written with the React/Next.js ecosystem in mind. Since this project is a raw CodeIgniter 3 application using HTML5, PHP, and native JS/AJAX, you MUST translate framework-specific terminology into their native equivalents before reporting. For example, instead of demanding `htmlFor`, require `for`; instead of `<Link>`, require `<a>`; instead of camelCase events like `onKeyDown`, require `onkeydown`; instead of `spellCheck={false}`, require `spellcheck="false"`. Do not suggest React components or JSX syntax.
