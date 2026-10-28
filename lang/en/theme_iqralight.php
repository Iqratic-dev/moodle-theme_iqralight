<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for theme_iqralight (en).
 *
 * @package    theme_iqralight
 * @category   string
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitynav'] = 'Activity navigation';
$string['advanced'] = 'Advanced';
$string['allmycourses'] = 'See all my courses';
$string['appearance'] = 'Appearance';
$string['audit'] = 'Iqralight: lightness audit';
$string['audit_activity'] = 'Activity';
$string['audit_block'] = 'Block';
$string['audit_blocks'] = 'Blocks shown to learners';
$string['audit_blocks_intro'] = 'Every block adds content, and often scripts, to the pages where it appears. Remove the blocks learners do not need.';
$string['audit_blocks_none'] = 'No block is shown on learners\' dashboards, courses or activities.';
$string['audit_check_cachejs'] = 'JavaScript caching is enabled.';
$string['audit_check_cachejs_fix'] = 'Enable it in Appearance > AJAX and Javascript.';
$string['audit_check_completion'] = 'Activity completion is enabled.';
$string['audit_check_completion_fix'] = 'Enable it in Advanced features so that progress and Continue work.';
$string['audit_check_designermode'] = 'Theme designer mode is off.';
$string['audit_check_designermode_fix'] = 'Turn it off in Appearance > Themes > Theme settings: it disables every cache.';
$string['audit_check_filter'] = 'The Iqralight filter is enabled.';
$string['audit_check_filter_fix'] = 'Install and enable it in Plugins > Filters.';
$string['audit_check_filterorder'] = 'The Iqralight filter runs after Multimedia plugins.';
$string['audit_check_filterorder_fix'] = 'Move it below Multimedia plugins in Plugins > Filters.';
$string['audit_check_passwordtoggle'] = 'Learners can show their password while typing it.';
$string['audit_check_passwordtoggle_fix'] = 'Enable "Toggle password visibility" in Plugins > Authentication > Manage authentication.';
$string['audit_check_serviceworker'] = 'The service worker is enabled.';
$string['audit_check_serviceworker_fix'] = 'Tick it in the Iqralight lightness settings, unless your IT policy forbids it.';
$string['audit_check_slasharguments'] = 'File addresses use slash arguments.';
$string['audit_check_slasharguments_fix'] = 'Enable slasharguments in Server > HTTP: Iqralight\'s fonts and cache need it.';
$string['audit_checks'] = 'Settings checklist';
$string['audit_component'] = 'Component';
$string['audit_course'] = 'Course';
$string['audit_dryrun'] = 'Tap to load: dry run';
$string['audit_dryrun_intro'] = 'Choose a course to see what the Iqralight filter would make wait for a tap. Nothing is changed.';
$string['audit_dryrun_nofilter'] = 'Install the Iqralight filter to use this dry run.';
$string['audit_dryrun_nothing'] = 'Nothing in this course would wait for a tap.';
$string['audit_file'] = 'File';
$string['audit_files'] = 'Heaviest course files';
$string['audit_files_none'] = 'No course file is heavier than 100 KB.';
$string['audit_filesize'] = 'Size';
$string['audit_firstvisit'] = 'First visit of a learner';
$string['audit_firstvisit_note'] = 'Estimate without the page itself (20 to 30 KB) or course content. Later visits reuse these files from the device\'s cache.';
$string['audit_icons'] = 'Icons outside the light fonts';
$string['audit_icons_none'] = 'Every icon used on this site is in the light icon fonts.';
$string['audit_icons_some'] = '{$a} icons are missing from the light fonts: on pages that show them, browsers download the full font. Rebuild the fonts with cli/build_icon_fonts.py.';
$string['audit_instances'] = 'Instances';
$string['audit_intro'] = 'What weighs on your learners\' connections on this site, measured from the files Moodle serves. Nothing is changed here.';
$string['audit_javascript'] = 'Scripts by plugin';
$string['audit_javascript_advice'] = 'Uninstalling plugins nobody uses lightens this script for every learner. It is your decision: review them in the <a href="{$a}">plugins overview</a>.';
$string['audit_javascript_intro'] = '{$a->count} components add scripts: {$a->raw} in total, {$a->compressed} once compressed, downloaded by every new visitor.';
$string['audit_kind'] = 'Content';
$string['audit_kind_document'] = 'Document';
$string['audit_kind_image'] = 'Image';
$string['audit_kind_video'] = 'Video';
$string['audit_part'] = 'Part';
$string['audit_part_amd'] = 'Moodle scripts (every installed plugin)';
$string['audit_part_css'] = 'Stylesheet';
$string['audit_part_fonts'] = 'Icon fonts (light)';
$string['audit_part_otherjs'] = 'Other core scripts';
$string['audit_size'] = 'Size (compressed)';
$string['audit_standard'] = 'Standard Moodle';
$string['audit_thirdparty'] = 'Third-party plugin';
$string['audit_total'] = 'Total';
$string['audit_type'] = 'Type';
$string['autotextonly'] = 'Text only on very slow connections';
$string['autotextonly_desc'] = 'On a very slow connection (2G), images wait for a tap even if they are light. Requires the Iqralight filter.';
$string['backtocourse'] = 'Back to course';
$string['brandcolor'] = 'Brand colour';
$string['brandcolor_contrast'] = 'White text on this colour is hard to read (contrast {$a->ratio}:1, at least 4.5:1 is needed). Try {$a->suggestion}, a darker shade of the same colour.';
$string['brandcolor_desc'] = 'The main colour of buttons, links and highlights. Iqralight derives every other shade from it and checks that white text on it stays readable.';
$string['brandcolor_format'] = 'Enter a colour as #RRGGBB, for example #0B6E5A.';
$string['chapter'] = 'Chapter {$a}';
$string['choosereadme'] = 'Iqralight is a Moodle theme for learners with very weak, costly or unstable connections: simple, modern, and light on data.';
$string['cli_dryrun'] = 'Dry run: nothing was changed.';
$string['cli_help'] = 'Applies an Iqralight preset.

Options:
-l, --list         List the shipped presets
-p, --preset=NAME  Apply a shipped preset
-f, --file=PATH    Apply an exported configuration
-n, --dry-run      Show the changes without applying them
-h, --help         Print this help';
$string['cli_unreadable'] = 'Cannot read {$a}.';
$string['configtitle'] = 'Iqralight';
$string['continue'] = 'Continue';
$string['contrast'] = 'High contrast';
$string['contrast_help'] = 'Black text, thick borders, underlined links.';
$string['counter'] = 'Data counter';
$string['coursereport'] = 'Course lightness';
$string['coursereport_advice'] = 'How to lighten the course';
$string['coursereport_advice_images'] = 'Keep images under 300 KB: a width of 1200 pixels is enough for a phone.';
$string['coursereport_advice_pdf'] = 'Compress PDF files before adding them: scanned documents often shrink tenfold.';
$string['coursereport_advice_text'] = 'Write the key points as text in a page: text weighs almost nothing and can be read aloud.';
$string['coursereport_advice_video'] = 'Host videos on a video platform and link them, rather than uploading them to the course.';
$string['coursereport_files'] = 'Heaviest files of the course';
$string['coursereport_intro'] = 'What weighs on your learners\' connections in this course, and how to lighten it. Nothing is changed here.';
$string['coursereport_waiting'] = 'What waits for a tap in data saver mode';
$string['credit'] = 'Iqralight is designed and maintained by <a href="{$a}">IQRATIC</a>, for institutions training learners with low connectivity: deployment, teacher training and support.';
$string['currentvalue'] = 'Current value';
$string['darkmode'] = 'Dark mode';
$string['darkmode_auto'] = 'Automatic: follows the device';
$string['darkmode_desc'] = 'Applies to learner pages that have been checked in dark: My courses, course pages, page, forum, assignment, quiz home, book, messages, profile, grades, calendar, preferences and login. Every other page stays light, and teachers\' content keeps a light surface so that its colours stay readable.';
$string['darkmode_off'] = 'Off';
$string['darkmode_on'] = 'Always';
$string['dataavoided'] = 'Data avoided';
$string['defaultmode'] = 'Default connection mode';
$string['defaultmode_desc'] = 'Applies until a learner chooses their own mode. Automatic detects the connection quality on each device.';
$string['display'] = 'Display';
$string['done'] = 'Done';
$string['easyread'] = 'Easy reading';
$string['easyread_help'] = 'Larger text and icons, and a Listen button that reads the page aloud.';
$string['exportconfig'] = 'Export the configuration';
$string['exportimport'] = 'Export and import';
$string['greeting'] = 'Hello, {$a}';
$string['importfile'] = 'Configuration file (JSON)';
$string['importpreview'] = 'Preview the changes';
$string['keepoffline'] = 'Keep offline';
$string['lightness'] = 'Lightness';
$string['listen'] = 'Listen';
$string['logos'] = 'Logo';
$string['logos_desc'] = 'Iqralight uses the site logo set in <a href="{$a}">Appearance &gt; Logos</a> (PNG or JPG). Keep it under 50 KB: the setup wizard refuses larger files.';
$string['logotoolarge'] = 'The logo weighs {$a}: at most 50 KB is accepted.';
$string['mbcurrency'] = 'Currency';
$string['mbcurrency_desc'] = 'Shown after the amount, for example FC, MAD or USD.';
$string['mbprice'] = 'Local price of 1 MB of mobile data';
$string['mbprice_desc'] = 'Optional. When set with a currency, learners and the report also see the money avoided. Use a dot for decimals.';
$string['mode_auto'] = 'Automatic';
$string['mode_auto_help'] = 'Iqralight adapts to your connection.';
$string['mode_auto_help_easy'] = 'Iqralight chooses for you.';
$string['mode_economy'] = 'Data saver';
$string['mode_economy_help'] = 'Images and heavy content wait for your tap.';
$string['mode_economy_help_easy'] = 'Images wait for your tap.';
$string['mode_full'] = 'Load everything';
$string['mode_full_help'] = 'Everything loads straight away.';
$string['mode_full_help_easy'] = 'Everything shows at once.';
$string['modechip_economy'] = 'Data saver';
$string['modechip_full'] = 'Everything';
$string['modeintro'] = 'Choose how Iqralight uses your data on this device and the others you sign in on.';
$string['modeintro_easy'] = 'Choose how pages load.';
$string['modetitle'] = 'Connection mode';
$string['mycourses'] = 'My courses';
$string['nav_account'] = 'My account';
$string['nav_courses'] = 'My courses';
$string['nav_label'] = 'Main navigation';
$string['nav_messages'] = 'Messages';
$string['newvalue'] = 'New value';
$string['nextactivity'] = 'Next: {$a}';
$string['offline_kept'] = 'Available on this device';
$string['offline_message'] = 'The page will reload as soon as the connection is back.';
$string['offline_none'] = 'Nothing has been kept on this device.';
$string['offline_retry'] = 'Try again';
$string['offline_title'] = 'Connection lost';
$string['offlineavailable'] = 'Available offline on this device.';
$string['offlinefailed'] = 'This resource could not be kept. Try again with a better connection.';
$string['offlinefailed_easy'] = 'It did not work. Try again with a better network.';
$string['offlinefull'] = 'Not enough room: at most {$a} MB can be kept on this device. Remove a kept resource first.';
$string['offlinefull_easy'] = 'No more room on this phone. Remove a kept lesson first.';
$string['offlinequota'] = 'Offline room per device (MB)';
$string['offlinequota_desc'] = 'Between 10 and 200 MB.';
$string['offlinereading'] = 'Offline reading';
$string['offlinereading_desc'] = 'Learners can keep pages, book chapters and PDFs on their device and read them without network. Kept resources are erased on logout, when another person logs in on the device, and after 30 days without reading. Leave off on shared computers. Requires the service worker.';
$string['offlinesaving'] = 'Saving…';
$string['offlinesubmit'] = 'No connection: your message was not sent. What you typed is kept here; send it again when the network is back.';
$string['offlinesubmit_easy'] = 'No network. Your text is kept. Send it again later.';
$string['opencourse'] = 'Open the course';
$string['percentdone'] = '{$a} %';
$string['pluginname'] = 'Iqralight';
$string['preset_prudent'] = 'Cautious';
$string['preset_prudent_desc'] = 'No service worker; anonymous counter. For a first trial or a strict IT policy.';
$string['preset_rural'] = 'Rural institution';
$string['preset_rural_desc'] = 'Data saver by default, text only on 2G, images over 200 KB on demand.';
$string['preset_sharedroom'] = 'Shared computer room';
$string['preset_sharedroom_desc'] = 'Automatic mode, anonymous counter: several people use the same computers.';
$string['preset_university'] = 'University with low bandwidth';
$string['preset_university_desc'] = 'Automatic mode, text only on 2G, images over 500 KB on demand.';
$string['presetapplied'] = 'The preset has been applied.';
$string['presetapply'] = 'Apply these changes';
$string['preseterror_format'] = 'This file is not an Iqralight configuration.';
$string['preseterror_logo'] = 'The logo in this file is not a valid PNG or JPG image.';
$string['preseterror_unknown'] = 'Unknown setting: {$a}.';
$string['preseterror_value'] = 'Invalid value for {$a}.';
$string['presetnochange'] = 'This configuration is already in place: nothing would change.';
$string['presetpreview'] = 'Changes that will be applied';
$string['presetpreviewlink'] = 'Preview';
$string['presets'] = 'Iqralight: presets';
$string['presets_intro'] = 'Apply a configuration suited to your institution. Every change is shown before it is applied.';
$string['presetunknown'] = 'Unknown preset: {$a}.';
$string['previousactivity'] = 'Previous';
$string['privacy:metadata:preference:contrast'] = 'Whether the learner chose the high contrast display.';
$string['privacy:metadata:preference:easyread'] = 'Whether the learner chose the easy reading display.';
$string['privacy:metadata:preference:mode'] = 'The connection mode the learner chose: automatic, data saver or load everything.';
$string['privacy:metadata:preference:textonly'] = 'Whether the learner chose to show text only, images loading on a tap.';
$string['privacy:metadata:stats'] = 'Network data each learner avoided per day, kept for a limited time and then folded into anonymous monthly totals.';
$string['privacy:metadata:stats:cachebytes'] = 'Bytes served by the device\'s cache instead of the network.';
$string['privacy:metadata:stats:day'] = 'The day concerned.';
$string['privacy:metadata:stats:mediabytes'] = 'Bytes of media the learner chose not to load.';
$string['privacy:metadata:stats:userid'] = 'The learner.';
$string['privacy:request:preference:contrast'] = 'High contrast: {$a}';
$string['privacy:request:preference:easyread'] = 'Easy reading: {$a}';
$string['privacy:request:preference:mode'] = 'Connection mode: {$a}';
$string['privacy:request:preference:textonly'] = 'Text only: {$a}';
$string['profile'] = 'Profile';
$string['profile_light'] = 'Data saver by default';
$string['profile_lighter'] = 'Data saver and text only by default';
$string['profile_site'] = 'Site settings';
$string['profiles'] = 'Iqralight: category profiles';
$string['profiles_intro'] = 'Make some categories lighter than the rest of the site, for example a rural centre. A profile only lightens, and applies to subcategories too. A learner\'s own choice always comes first.';
$string['profiles_nocategory'] = 'There is no course category yet.';
$string['profilessaved'] = 'The profiles have been saved.';
$string['progress'] = '{$a->done} of {$a->total} done';
$string['progresslabel'] = 'Course progress';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'SCSS or CSS rules, added after the theme\'s own rules. Keep them light: every line is sent to every learner.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'SCSS variables, compiled before the theme. Use it to change a variable, for example $border-radius: 0.5rem;';
$string['region-side-pre'] = 'Right';
$string['removeoffline'] = 'Remove from this device';
$string['report_anonymous'] = 'The counter is anonymous: only site totals are kept, learners do not see a personal total.';
$string['report_cache'] = 'Served by device cache (MB)';
$string['report_download'] = 'Download the report';
$string['report_intro'] = 'Network data Iqralight avoided on this site, per month. The two categories are kept apart and figures are rounded down.';
$string['report_learners'] = 'Learners';
$string['report_media'] = 'Media not loaded (MB)';
$string['report_money'] = 'Money avoided ({$a})';
$string['report_month'] = 'Month';
$string['report_title'] = 'Iqralight: data avoided';
$string['report_total'] = 'Total (MB)';
$string['resume'] = 'Pick up where you left off';
$string['resume_easy'] = 'Continue my course';
$string['review'] = 'Review';
$string['saved'] = 'Iqralight spared you {$a} MB of data this month.';
$string['saved_easy'] = '{$a} MB saved this month.';
$string['savedata'] = 'Respect the browser\'s data saver';
$string['savedata_desc'] = 'When a phone\'s data saver is on, the very first page is already light.';
$string['savedmoney'] = 'That is about {$a->amount} {$a->currency}.';
$string['serviceworker'] = 'Keep the site\'s resources on devices';
$string['serviceworker_desc'] = 'Styles, scripts and icons are downloaded once per device instead of after every cache clean-up, and a clear page is shown when the connection drops. Pages, files and personal data are never stored. Untick to remove it from every device at their next visit.';
$string['setting'] = 'Setting';
$string['setup'] = 'Iqralight: setup wizard';
$string['setup_alreadyactive'] = 'Already active.';
$string['setup_colour'] = 'Your colour';
$string['setup_colour_custom'] = 'Colour (#RRGGBB)';
$string['setup_colour_intro'] = 'Choose the colour of buttons and highlights. All the colours offered stay readable with white text.';
$string['setup_colour_other'] = 'Another colour';
$string['setup_completion'] = 'Activity completion';
$string['setup_completion_desc'] = 'Enable it so that learners see their progress and the Continue button knows where they stopped.';
$string['setup_done'] = 'Iqralight is set up.';
$string['setup_finish'] = 'Finish';
$string['setup_homepage'] = 'My courses as home page';
$string['setup_homepage_desc'] = 'Learners land on My courses, with the Resume card, right after logging in.';
$string['setup_logo'] = 'Your logo';
$string['setup_logo_intro'] = 'A PNG or JPG file of 50 KB at most, shown on the login page. You can skip this step.';
$string['setup_native'] = 'Moodle settings worth enabling';
$string['setup_native_intro'] = 'Iqralight never changes Moodle\'s own settings by itself. Tick the ones you want to enable.';
$string['setup_next'] = 'Next';
$string['setup_profile'] = 'Your institution';
$string['setup_profile_intro'] = 'Choose the profile closest to your learners\' situation. You can change it later in Presets.';
$string['setup_profile_keep'] = 'Keep the current settings';
$string['setup_step'] = 'Step {$a->step} of {$a->total}';
$string['start'] = 'Start';
$string['stats'] = 'Count the data avoided';
$string['stats_desc'] = 'Learners see what Iqralight spared them this month, and administrators get a monthly report. Browsers send their figures at most every ten minutes.';
$string['statsanonymous'] = 'Anonymous counter';
$string['statsanonymous_desc'] = 'Keep only site totals, with no row per learner. Learners then see no personal total.';
$string['statsretention'] = 'Keep learner figures for (days)';
$string['statsretention_desc'] = 'After this period they are folded into anonymous monthly totals. Between 1 and 365 days.';
$string['stoplistening'] = 'Stop';
$string['task_aggregate_stats'] = 'Fold old data counter figures into monthly totals';
$string['textonly'] = 'Text only';
$string['textonly_help'] = 'Every image waits for your tap.';
$string['textonly_help_easy'] = 'No images, unless you tap.';
$string['unreadmessages'] = '{$a} unread conversations';
$string['verb_assign'] = 'Submit my work';
$string['verb_bigbluebuttonbn'] = 'Join the class';
$string['verb_book'] = 'Read';
$string['verb_choice'] = 'Answer';
$string['verb_data'] = 'Look up';
$string['verb_default'] = 'Open';
$string['verb_feedback'] = 'Answer';
$string['verb_folder'] = 'Open the folder';
$string['verb_forum'] = 'Discuss';
$string['verb_glossary'] = 'Look up';
$string['verb_h5pactivity'] = 'Do the activity';
$string['verb_imscp'] = 'Read';
$string['verb_lesson'] = 'Follow the lesson';
$string['verb_page'] = 'Read';
$string['verb_quiz'] = 'Take the quiz';
$string['verb_resource'] = 'Download ({$a})';
$string['verb_scorm'] = 'Follow the module';
$string['verb_url'] = 'Open the link';
$string['verb_watch'] = 'Watch ({$a})';
$string['verb_wiki'] = 'Contribute';
$string['verb_workshop'] = 'Take part';
$string['wizardlinks'] = 'Set up Iqralight in four steps with the <a href="{$a->setup}">setup wizard</a>, or apply a <a href="{$a->presets}">preset</a>.';
