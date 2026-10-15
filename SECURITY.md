# Security Policy

## Supported versions

The latest released `0.x` version receives fixes. Older releases are not maintained.

## Reporting a vulnerability

Please do not open a public issue for security problems.

Send a private report to the maintainer (`bugo@dragomano.ru`), or use the "Report a vulnerability"
form in the Security tab of the repository. Please include:

- the affected version and how the package is used (SMF admin panel, portal, own code);
- a minimal reproduction (code snippet, request or rendered output);
- your assessment of the impact.

You will get an answer as soon as possible, together with a note about the fix and the release that
contains it.

## Scope notes

The package renders HTML for the SMF admin panel, so some APIs intentionally accept raw HTML:

- `TableRenderer` and `BreadcrumbRenderer` insert the strings they are given (`setHtml`), which is
  required for SMF markup (icons, buttons, BBC links) — data coming from users must be escaped
  or sanitized by the caller **before** it is passed to these APIs;
- `FormBuilder::setScript()` and `FormBuilder::setStyle()` insert a script block and inline styles;
- `Field::setAttributes()` accepts arbitrary HTML attributes, including event handlers, so attribute
  names must not come from user input.

The library does not escape these values for you: escaping is the responsibility of the caller.
