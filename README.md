# Iqralight — Moodle theme for low connectivity

Iqralight is a Moodle theme for learners with very weak, costly or unstable
connections: simple, modern, and light on data. It extends Boost and never
modifies Moodle core. Iqralight inherits Boost's layouts unchanged and adds its own
elements through the renderer only, so it follows each Moodle version's Boost.

## Compatibility

Moodle 5.0 to 5.3. Tested on Moodle 5.0 with PostgreSQL 16 and on Moodle 5.2
with MariaDB 10.11. Moodle 5.3 requires PostgreSQL 17 or MariaDB 11.4: it is
covered by the continuous integration (GitHub Actions) for both databases.

**Version 0.16.1 (alpha)** — Moodle 5.0 and later. Target: Moodle 5.3 LTS.

## Layout on every screen

Mobile first: the phone design is the base. From 768 px, the learner's home and My
courses use the width of the screen up to 1200 px, with the greeting beside the
*Resume* card and the home blocks in two columns from 1200 px; course pages keep a
reading width of about 75 characters a line. The site logo is shown in the top bar,
even when only the main logo has been uploaded.

## Components

One system on every page: three button styles (filled for the main action, tinted
for the others, text for minor ones), fields with a visible frame, due-date labels
that always carry a word, flat blocks with a hairline, a compact calendar block and a
keyboard focus ring that is always visible. Colours are CSS variables (`--iqra-*`),
redefined by the dark mode. The learner's home shows the greeting with the date and
the data saved, a Resume band (course, activity, progress, Continue) and the three
last visited courses.

## Your own SCSS

*Advanced* tab of the theme settings: *Raw initial SCSS* (variables, compiled before the
theme, for example `$border-radius: 0.5rem;` or `$iqra-wide-maxwidth: 1320px;`) and
*Raw SCSS* (rules added after the theme's). Keep them light: every line is sent to
every learner.

## Installation

1. Unzip into `theme/iqralight` (the folder must be named `iqra`), or use
   *Site administration > Plugins > Install plugins*.
2. Run the upgrade: *Site administration > Notifications*.
3. Select the theme: *Site administration > Appearance > Themes*.
4. Run the setup wizard: *Appearance > Themes > Iqralight: setup wizard*
   (colour, logo, institution profile, then the Moodle settings worth
   enabling, each one only if you tick it).

Hosts deploying several sites can apply a preset from the command line:

    php theme/iqralight/cli/install.php --list
    php theme/iqralight/cli/install.php --preset=rural --dry-run
    php theme/iqralight/cli/install.php --file=iqra-config.json

## What this version does

- "Resume" card on *My courses*: last course, last activity, progress.
- Bottom navigation bar for learners on small screens.
- Course page for learners: an action verb above each activity (Read, Watch,
  Download with the file size, Take the quiz, Submit my work, Discuss...) and
  "Chapter N" above each section title. Course formats Topics and Weeks.
- *My courses*: a large button on each card (Start, or Continue), and the
  Resume card says Start, Continue or Review depending on progress. Course
  images are shown in every view: the original in *Load everything* mode, a
  250-pixel thumbnail served by Moodle in data saver mode (about 30 times
  lighter), none in text-only mode.
- Connection mode (automatic, data saver, load everything), chosen by the
  learner and stored as a user preference. Respects the browser's
  `Save-Data` header. 
- Brand colour setting that refuses colours unreadable under white text.
- Smooth page transitions (View Transitions) and tap feedback.
- Teachers in editing mode see the standard Boost interface.
- Light icon fonts: the Font Awesome fonts are reduced to the 270 icons
  Moodle actually uses (179 KB down to 26 KB per page). A full font is only
  downloaded if a page shows an icon outside that list, so nothing can break.
- Service worker: styles, scripts, fonts and theme images are downloaded once
  per device; a light offline page replaces the browser's error when the
  connection drops, and reloads by itself when it is back. Pages, files, AJAX
  calls and form submissions are never cached. Caches are cleared on logout and
  on each upgrade. Untick *Keep the site's resources on devices* to remove it
  from every device at their next visit.
- Data saver mode and profile pictures: initials on a coloured disc where
  Moodle builds the picture, and a 213-byte silhouette served by the service
  worker elsewhere (forums, messaging). No profile photo is downloaded.
- Without network, a form is not sent (forum post, message, profile): the
  learner keeps what they typed and sees a clear message. Quizzes,
  assignments and the login page keep Moodle's own behaviour.
- Offline reading (off by default, on in the *Rural institution* preset): a
  *Keep offline* button on pages, book chapters and PDFs shown in Moodle saves
  the page and its course files on the device (50 MB per device by default).
  Without network, kept resources open normally and the offline page lists
  them. They are erased on logout, when another person logs in on the device,
  and after 30 days without reading. Never offered on quizzes, assignments,
  forums, messages or profiles.
- Strings in English, French and Arabic.

- *Text only* option, chosen by the learner or automatic on 2G: every image
  waits for a tap. Requires the companion filter **filter_iqralight**
  ("tap to load"), which also defers heavy videos, PDFs and images.

- Data counter: learners see on *My courses* and on their profile the network
  data Iqralight spared them this month (and the money, when the site sets the
  local price of 1 MB). Two categories are kept apart: media never loaded and
  resources served by the device cache. Browsers report at most every ten
  minutes. Administrators get a monthly report with CSV export
  (*Appearance > Themes > Iqralight: data avoided*). Learner figures are folded into
  anonymous monthly totals after 90 days (configurable); an anonymous mode keeps
  site totals only. Covered by the Privacy API.

- Display options chosen by each learner in the mode window:
  *High contrast* (black text, thick borders, underlined links, action colour
  darkened to at least 7:1) and *Easy reading* (larger text and touch targets,
  and a *Listen* button that reads the page aloud paragraph by paragraph,
  highlighting the one being read). Listening uses the phone's own speech
  synthesis: no audio is downloaded, the button only appears when the device
  has a voice for the page language, and never during a quiz attempt.

- Presets (*Iqralight: presets*): rural institution, shared computer room,
  university with low bandwidth, cautious. Every change is previewed before it
  is applied; the whole configuration, logo included, can be exported to a
  JSON file and imported on another site, with every value checked first.
- Large *Next* button at the bottom of each activity for learners, and *Back
  to course* on the last one. Moodle hides its own links when the theme uses
  the course index, so Iqralight renders this button itself; editing mode keeps
  Moodle's behaviour.

- Category profiles (*Iqralight: category profiles*): make some categories lighter
  than the rest of the site, for example a rural centre (data saver by default,
  or data saver and text only). A profile only lightens and applies to
  subcategories; a learner's own choice always comes first.
- Easy reading also uses simpler wording for Iqralight's own texts.
- Lightness audit (*Iqralight: lightness audit*): estimated weight of a learner's
  first visit, settings checklist, script weight of every installed plugin
  (standard or third-party), icons missing from the light fonts, heaviest
  course files, and a dry run of the "tap to load" filter on any course.
  Nothing is changed by the audit. It also lists the blocks shown to learners,
  with how often they appear and the script weight each one adds.
- *Course lightness* report for teachers, in the course menu (capability
  moodle/course:update): heaviest files of the course, what waits for a tap in
  data saver mode, and advice to lighten the course.

- Dark mode (*Appearance > Themes > Iqralight*: automatic, off or always), on the
  learner pages checked in dark only: My courses, course pages, page, forum,
  assignment, quiz home, book, messages, profile, grades, calendar,
  preferences and login. Every other page stays light, editing mode and the
  high contrast display stay light, and teachers' content keeps a light
  surface so that its colours stay readable. Adds 3.4 KB to the stylesheet.

## Not yet in this version

Dark mode for quiz attempts, lessons and other pages not listed above, once
they have been checked. See the specification for the roadmap.

## Automated tests

PHPUnit tests cover the connection mode, category profiles, colour contrast, data
counter, presets (export and import included), course page verbs and the Privacy
API; Behat scenarios cover the course page, the home page and the activity
navigation. The companion filter has its own PHPUnit and Behat tests. They run in the
continuous integration on Moodle 5.0 to 5.3, PostgreSQL and MariaDB.

## Mobile checks

Every learner page is checked in a real Chromium at 360 and 390 pixels (Moodle 5.0
and 5.2): no horizontal overflow, touch targets of at least 44 pixels for what
learners tap often, no JavaScript error. On phones, the Messages item of the bottom
bar opens Moodle's messaging drawer in full screen, and the dashboard's edit mode
switch is hidden for learners. Pages are also checked with WebKit, Safari's engine,
at 375 and 393 pixels. A learner page never scrolls sideways, and the Listen button
of easy reading steps aside while a drawer is open. Rerun the checks with
cli/mobile_audit.js (Chromium) and cli/safari_audit.py (WebKit).

## Development

After each Moodle upgrade, rebuild the light icon fonts:

    pip install fonttools brotli
    python3 cli/build_icon_fonts.py --moodle /path/to/moodle --css compiled.css [--moodle ... --css ...]
    python3 cli/build_dark_mode.py --css compiled.css --pages cli/dark-pages.json

where `compiled.css` is the stylesheet served by `theme/styles.php/iqralight/.../all`.

The JavaScript module is in `amd/src/iqra.js`; rebuild `amd/build` with
`npx grunt amd` from the Moodle root after any change.

## About

Iqralight is designed and maintained by [IQRATIC](https://iqratic.com), for institutions
training learners with low connectivity: deployment, teacher training and support.

## Licence

GNU GPL v3 or later. © 2026 IQRATIC.
