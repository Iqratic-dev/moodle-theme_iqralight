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
 * Strings for theme_iqralight (ar).
 *
 * @package    theme_iqralight
 * @category   string
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitynav'] = 'التنقل بين الأنشطة';
$string['appearance'] = 'المظهر';
$string['audit'] = 'Iqralight: تدقيق الخفة';
$string['audit_activity'] = 'النشاط';
$string['audit_block'] = 'الكتلة';
$string['audit_blocks'] = 'الكتل المعروضة للمتعلّمين';
$string['audit_blocks_intro'] = 'تضيف كل كتلة محتوى، وغالبًا برامج نصية، إلى الصفحات التي تظهر فيها. أزل الكتل التي لا يحتاجها المتعلّمون.';
$string['audit_blocks_none'] = 'لا تُعرض أي كتلة في لوحات المتعلّمين أو دوراتهم أو أنشطتهم.';
$string['audit_check_cachejs'] = 'التخزين المؤقت لـ JavaScript مفعّل.';
$string['audit_check_cachejs_fix'] = 'فعّله في المظهر > AJAX وJavascript.';
$string['audit_check_completion'] = 'إتمام الأنشطة مفعّل.';
$string['audit_check_completion_fix'] = 'فعّله في الميزات المتقدمة ليعمل التقدم وزر تابِع.';
$string['audit_check_designermode'] = 'وضع مصمم القالب معطَّل.';
$string['audit_check_designermode_fix'] = 'عطّله في المظهر > القوالب > إعدادات القوالب: فهو يعطّل كل أنواع التخزين المؤقت.';
$string['audit_check_filter'] = 'مرشّح Iqralight مفعّل.';
$string['audit_check_filter_fix'] = 'ثبّته وفعّله في الإضافات > المرشّحات.';
$string['audit_check_filterorder'] = 'يعمل مرشّح Iqralight بعد مرشّح الوسائط المتعددة.';
$string['audit_check_filterorder_fix'] = 'انقله أسفل الوسائط المتعددة في الإضافات > المرشّحات.';
$string['audit_check_passwordtoggle'] = 'يمكن للمتعلّمين إظهار كلمة المرور أثناء كتابتها.';
$string['audit_check_passwordtoggle_fix'] = 'فعّل «إظهار كلمة المرور» في الإضافات > المصادقة > إدارة المصادقة.';
$string['audit_check_serviceworker'] = 'عامل الخدمة مفعّل.';
$string['audit_check_serviceworker_fix'] = 'حدّده في إعدادات التخفيف في Iqralight، إلا إذا منعته سياستك المعلوماتية.';
$string['audit_check_slasharguments'] = 'تستخدم عناوين الملفات وسائط الشرطة المائلة.';
$string['audit_check_slasharguments_fix'] = 'فعّل ⁦slasharguments⁩ في الخادم > HTTP: تحتاجه خطوط Iqralight وذاكرته المؤقتة.';
$string['audit_checks'] = 'قائمة التحقق من الإعدادات';
$string['audit_component'] = 'المكوّن';
$string['audit_course'] = 'الدورة';
$string['audit_dryrun'] = 'التحميل عند النقر: تشغيل تجريبي';
$string['audit_dryrun_intro'] = 'اختر دورة لترى ما سيجعله مرشّح Iqralight ينتظر نقرة. لا يتم تغيير أي شيء.';
$string['audit_dryrun_nofilter'] = 'ثبّت مرشّح Iqralight لاستخدام هذا التشغيل التجريبي.';
$string['audit_dryrun_nothing'] = 'لا شيء في هذه الدورة سينتظر نقرة.';
$string['audit_file'] = 'الملف';
$string['audit_files'] = 'أثقل ملفات الدورات';
$string['audit_files_none'] = 'لا يتجاوز أي ملف دورة 100 كيلوبايت.';
$string['audit_filesize'] = 'الحجم';
$string['audit_firstvisit'] = 'الزيارة الأولى للمتعلّم';
$string['audit_firstvisit_note'] = 'تقدير دون الصفحة نفسها (20 إلى 30 كيلوبايت) ودون محتوى الدورات. تعيد الزيارات اللاحقة استخدام هذه الملفات من ذاكرة الجهاز.';
$string['audit_icons'] = 'أيقونات غير موجودة في الخطوط الخفيفة';
$string['audit_icons_none'] = 'جميع الأيقونات المستخدمة في هذا الموقع موجودة في الخطوط الخفيفة.';
$string['audit_icons_some'] = '⁦{$a}⁩ أيقونات غير موجودة في الخطوط الخفيفة: في الصفحات التي تعرضها، يُنزّل المتصفح الخط الكامل. أعد توليد الخطوط باستخدام ⁦cli/build_icon_fonts.py⁩.';
$string['audit_instances'] = 'عدد المرات';
$string['audit_intro'] = 'ما يثقل اتصال متعلّميك على هذا الموقع، مقيسًا من الملفات التي يقدّمها Moodle. لا يتم تغيير أي شيء هنا.';
$string['audit_javascript'] = 'البرامج النصية حسب الإضافة';
$string['audit_javascript_advice'] = 'إزالة الإضافات التي لا يستخدمها أحد تخفّف هذا البرنامج النصي لكل متعلّم. القرار لك: راجعها في <a href="{$a}">نظرة عامة على الإضافات</a>.';
$string['audit_javascript_intro'] = '⁦{$a->count}⁩ مكوّنات تضيف برامج نصية: ⁦{$a->raw}⁩ إجمالًا، و⁦{$a->compressed}⁩ بعد الضغط، يُنزّلها كل زائر جديد.';
$string['audit_kind'] = 'المحتوى';
$string['audit_kind_document'] = 'مستند';
$string['audit_kind_image'] = 'صورة';
$string['audit_kind_video'] = 'فيديو';
$string['audit_part'] = 'العنصر';
$string['audit_part_amd'] = 'برامج Moodle النصية (كل الإضافات المثبتة)';
$string['audit_part_css'] = 'ورقة الأنماط';
$string['audit_part_fonts'] = 'خطوط الأيقونات (الخفيفة)';
$string['audit_part_otherjs'] = 'برامج نصية أخرى من النواة';
$string['audit_size'] = 'الحجم (مضغوط)';
$string['audit_standard'] = 'Moodle القياسي';
$string['audit_thirdparty'] = 'إضافة خارجية';
$string['audit_total'] = 'المجموع';
$string['audit_type'] = 'النوع';
$string['autotextonly'] = 'نص فقط في الاتصالات البطيئة جدًا';
$string['autotextonly_desc'] = 'في الاتصال البطيء جدًا (2G)، تنتظر الصور نقرة حتى لو كانت خفيفة. يتطلب مرشّح Iqralight.';
$string['backtocourse'] = 'العودة إلى الدورة';
$string['brandcolor'] = 'لون العلامة';
$string['brandcolor_contrast'] = 'يصعب قراءة النص الأبيض على هذا اللون (التباين ⁦{$a->ratio}:1⁩، والحد الأدنى 4.5:1). جرّب ⁦{$a->suggestion}⁩، وهو درجة أغمق من اللون نفسه.';
$string['brandcolor_desc'] = 'لون الأزرار والروابط والعناصر البارزة. يشتقّ Iqralight منه جميع الدرجات الأخرى ويتحقق من أن النص الأبيض يظل مقروءًا عليه.';
$string['brandcolor_format'] = 'أدخل اللون بصيغة ⁦#RRGGBB⁩، مثل ⁦#0B6E5A⁩.';
$string['chapter'] = 'الفصل ⁦{$a}⁩';
$string['choosereadme'] = 'Iqralight قالب Moodle للمتعلّمين ذوي الاتصال الضعيف جدًا أو المكلف أو غير المستقر: بسيط وحديث وموفّر للبيانات.';
$string['cli_dryrun'] = 'محاكاة: لم يتم تغيير أي شيء.';
$string['cli_help'] = 'يطبّق إعدادًا مسبقًا لـ Iqralight.

الخيارات:
-l, --list         عرض الإعدادات المسبقة المتوفرة
-p, --preset=NAME  تطبيق إعداد مسبق متوفر
-f, --file=PATH    تطبيق إعدادات مصدَّرة
-n, --dry-run      عرض التغييرات دون تطبيقها
-h, --help         عرض هذه المساعدة';
$string['cli_unreadable'] = 'تعذّرت قراءة ⁦{$a}⁩.';
$string['configtitle'] = 'Iqralight';
$string['continue'] = 'تابِع';
$string['contrast'] = 'تباين عالٍ';
$string['contrast_help'] = 'نص أسود وحدود سميكة وروابط مسطّرة.';
$string['counter'] = 'عدّاد البيانات';
$string['coursereport'] = 'خفة الدورة';
$string['coursereport_advice'] = 'كيف تخفف الدورة';
$string['coursereport_advice_images'] = 'اجعل الصور أقل من 300 كيلوبايت: عرض 1200 بكسل يكفي للهاتف.';
$string['coursereport_advice_pdf'] = 'اضغط ملفات PDF قبل إضافتها: يمكن غالبًا تصغير المستند الممسوح ضوئيًا عشر مرات.';
$string['coursereport_advice_text'] = 'اكتب النقاط الأساسية نصًا في صفحة: النص لا يكاد يزن شيئًا ويمكن قراءته بصوت عالٍ.';
$string['coursereport_advice_video'] = 'استضف مقاطع الفيديو على منصة فيديو وضع رابطًا لها بدل رفعها إلى الدورة.';
$string['coursereport_files'] = 'أثقل ملفات الدورة';
$string['coursereport_intro'] = 'ما يثقل اتصال متعلّميك في هذه الدورة، وكيف تخففه. لا يتم تغيير أي شيء هنا.';
$string['coursereport_waiting'] = 'ما ينتظر نقرة في وضع توفير البيانات';
$string['credit'] = 'صمّمت <a href="{$a}">IQRATIC</a> قالب Iqralight وتتولى صيانته، للمؤسسات التي تكوّن متعلّمين ذوي اتصال ضعيف: النشر، وتكوين المعلّمين، والمرافقة.';
$string['currentvalue'] = 'القيمة الحالية';
$string['darkmode'] = 'الوضع الداكن';
$string['darkmode_auto'] = 'تلقائي: يتبع إعداد الجهاز';
$string['darkmode_desc'] = 'يُطبَّق على صفحات المتعلّم التي تم التحقق منها بالوضع الداكن: دوراتي، صفحات الدورة، الصفحة، المنتدى، الواجب، الصفحة الرئيسية للاختبار، الكتاب، الرسائل، الملف الشخصي، الدرجات، التقويم، التفضيلات وتسجيل الدخول. تبقى كل الصفحات الأخرى فاتحة، وتحتفظ محتويات المعلّمين بخلفية فاتحة لتبقى ألوانها مقروءة.';
$string['darkmode_off'] = 'معطَّل';
$string['darkmode_on'] = 'دائمًا';
$string['dataavoided'] = 'البيانات التي تم تجنّبها';
$string['defaultmode'] = 'وضع الاتصال الافتراضي';
$string['defaultmode_desc'] = 'يُطبَّق ما لم يختر المتعلّم وضعه الخاص. يكشف الوضع التلقائي جودة الاتصال على كل جهاز.';
$string['display'] = 'العرض';
$string['done'] = 'تم';
$string['easyread'] = 'قراءة ميسّرة';
$string['easyread_help'] = 'نص وأيقونات أكبر، وزر استمع يقرأ الصفحة بصوت عالٍ.';
$string['exportconfig'] = 'تصدير الإعدادات';
$string['exportimport'] = 'التصدير والاستيراد';
$string['greeting'] = 'مرحبًا، ⁦{$a}⁩';
$string['importfile'] = 'ملف الإعدادات (JSON)';
$string['importpreview'] = 'معاينة التغييرات';
$string['keepoffline'] = 'الاحتفاظ به دون اتصال';
$string['lightness'] = 'التخفيف';
$string['listen'] = 'استمع';
$string['logos'] = 'الشعار';
$string['logos_desc'] = 'يستخدم Iqralight شعار الموقع المحدد في <a href="{$a}">المظهر &gt; الشعارات</a> (PNG أو JPG). اجعله أقل من 50 كيلوبايت: يرفض معالج الإعداد الملفات الأكبر.';
$string['logotoolarge'] = 'حجم الشعار ⁦{$a}⁩: الحد الأقصى المقبول 50 كيلوبايت.';
$string['mbcurrency'] = 'العملة';
$string['mbcurrency_desc'] = 'تظهر بعد المبلغ، مثل ⁦FC⁩ أو ⁦MAD⁩ أو ⁦USD⁩.';
$string['mbprice'] = 'السعر المحلي لـ 1 ميغابايت من بيانات الجوال';
$string['mbprice_desc'] = 'اختياري. عند إدخاله مع العملة، يرى المتعلّمون والتقرير أيضًا المبلغ المالي الموفَّر. استخدم النقطة للكسور العشرية.';
$string['mode_auto'] = 'تلقائي';
$string['mode_auto_help'] = 'يتكيّف Iqralight مع اتصالك.';
$string['mode_auto_help_easy'] = 'يختار Iqralight لك.';
$string['mode_economy'] = 'توفير البيانات';
$string['mode_economy_help'] = 'تنتظر الصور والمحتويات الثقيلة موافقتك.';
$string['mode_economy_help_easy'] = 'تنتظر الصور نقرتك.';
$string['mode_full'] = 'تحميل الكل';
$string['mode_full_help'] = 'يُحمَّل كل شيء فورًا.';
$string['mode_full_help_easy'] = 'يظهر كل شيء فورًا.';
$string['modechip_economy'] = 'توفير';
$string['modechip_full'] = 'تحميل الكل';
$string['modeintro'] = 'اختر كيف يستخدم Iqralight بياناتك على هذا الجهاز وعلى الأجهزة الأخرى التي تسجّل الدخول منها.';
$string['modeintro_easy'] = 'اختر كيف تُحمَّل الصفحات.';
$string['modetitle'] = 'وضع الاتصال';
$string['nav_account'] = 'حسابي';
$string['nav_courses'] = 'دوراتي';
$string['nav_label'] = 'التنقل الرئيسي';
$string['nav_messages'] = 'الرسائل';
$string['newvalue'] = 'القيمة الجديدة';
$string['nextactivity'] = 'التالي: ⁦{$a}⁩';
$string['offline_kept'] = 'متاح على هذا الجهاز';
$string['offline_message'] = 'ستُحمَّل الصفحة من جديد فور عودة الاتصال.';
$string['offline_none'] = 'لم يُحفظ أي شيء على هذا الجهاز.';
$string['offline_retry'] = 'أعد المحاولة';
$string['offline_title'] = 'انقطع الاتصال';
$string['offlineavailable'] = 'متاح دون اتصال على هذا الجهاز.';
$string['offlinefailed'] = 'تعذّر الاحتفاظ بهذا المورد. أعد المحاولة باتصال أفضل.';
$string['offlinefailed_easy'] = 'لم ينجح ذلك. أعد المحاولة بشبكة أفضل.';
$string['offlinefull'] = 'لا توجد مساحة كافية: يمكن الاحتفاظ بـ {$a} ميغابايت كحد أقصى على هذا الجهاز. أزل موردًا محفوظًا أولًا.';
$string['offlinefull_easy'] = 'لا مساحة على هذا الهاتف. أزل درسًا محفوظًا أولًا.';
$string['offlinequota'] = 'المساحة دون اتصال لكل جهاز (ميغابايت)';
$string['offlinequota_desc'] = 'بين 10 و200 ميغابايت.';
$string['offlinereading'] = 'القراءة دون اتصال';
$string['offlinereading_desc'] = 'يمكن للمتعلّمين الاحتفاظ بالصفحات وفصول الكتب وملفات PDF على أجهزتهم وقراءتها دون شبكة. تُمحى الموارد المحفوظة عند تسجيل الخروج، وعند دخول شخص آخر على الجهاز، وبعد 30 يومًا دون قراءة. اتركه معطّلًا على الأجهزة المشتركة. يتطلب عامل الخدمة.';
$string['offlinesaving'] = 'جارٍ الحفظ…';
$string['offlinesubmit'] = 'لا يوجد اتصال: لم يُرسَل ما كتبته. نصّك محفوظ هنا؛ أعد إرساله عند عودة الشبكة.';
$string['offlinesubmit_easy'] = 'لا توجد شبكة. نصّك محفوظ. أرسله لاحقًا.';
$string['opencourse'] = 'افتح الدورة';
$string['pluginname'] = 'Iqralight';
$string['preset_prudent'] = 'حذِر';
$string['preset_prudent_desc'] = 'دون عامل الخدمة؛ عدّاد مجهول الهوية. لتجربة أولى أو لسياسة معلوماتية صارمة.';
$string['preset_rural'] = 'مؤسسة ريفية';
$string['preset_rural_desc'] = 'توفير البيانات افتراضيًا، نص فقط في 2G، الصور التي تتجاوز 200 كيلوبايت عند الطلب.';
$string['preset_sharedroom'] = 'قاعة حواسيب مشتركة';
$string['preset_sharedroom_desc'] = 'الوضع التلقائي، عدّاد مجهول الهوية: يستخدم عدة أشخاص الأجهزة نفسها.';
$string['preset_university'] = 'جامعة ذات نطاق ترددي محدود';
$string['preset_university_desc'] = 'الوضع التلقائي، نص فقط في 2G، الصور التي تتجاوز 500 كيلوبايت عند الطلب.';
$string['presetapplied'] = 'تم تطبيق الإعداد المسبق.';
$string['presetapply'] = 'تطبيق هذه التغييرات';
$string['preseterror_format'] = 'هذا الملف ليس إعدادات Iqralight.';
$string['preseterror_logo'] = 'الشعار في هذا الملف ليس صورة PNG أو JPG صالحة.';
$string['preseterror_unknown'] = 'إعداد غير معروف: ⁦{$a}⁩.';
$string['preseterror_value'] = 'قيمة غير صالحة لـ ⁦{$a}⁩.';
$string['presetnochange'] = 'هذه الإعدادات مطبّقة بالفعل: لن يتغير شيء.';
$string['presetpreview'] = 'التغييرات التي ستُطبَّق';
$string['presetpreviewlink'] = 'معاينة';
$string['presets'] = 'Iqralight: الإعدادات المسبقة';
$string['presets_intro'] = 'طبّق إعدادات تناسب مؤسستك. يُعرض كل تغيير قبل تطبيقه.';
$string['presetunknown'] = 'إعداد مسبق غير معروف: ⁦{$a}⁩.';
$string['previousactivity'] = 'السابق';
$string['privacy:metadata:preference:contrast'] = 'ما إذا اختار المتعلّم عرض التباين العالي.';
$string['privacy:metadata:preference:easyread'] = 'ما إذا اختار المتعلّم عرض القراءة الميسّرة.';
$string['privacy:metadata:preference:mode'] = 'وضع الاتصال الذي اختاره المتعلّم: تلقائي أو توفير البيانات أو تحميل الكل.';
$string['privacy:metadata:preference:textonly'] = 'ما إذا اختار المتعلّم عرض النص فقط، مع تحميل الصور عند النقر.';
$string['privacy:metadata:stats'] = 'البيانات الشبكية التي تجنّبها كل متعلّم يوميًا، تُحفظ لمدة محدودة ثم تُدمج في مجاميع شهرية مجهولة الهوية.';
$string['privacy:metadata:stats:cachebytes'] = 'البايتات المقدَّمة من ذاكرة الجهاز بدل الشبكة.';
$string['privacy:metadata:stats:day'] = 'اليوم المعني.';
$string['privacy:metadata:stats:mediabytes'] = 'بايتات الوسائط التي لم يحمّلها المتعلّم.';
$string['privacy:metadata:stats:userid'] = 'المتعلّم.';
$string['privacy:request:preference:contrast'] = 'تباين عالٍ: {$a}';
$string['privacy:request:preference:easyread'] = 'قراءة ميسّرة: {$a}';
$string['privacy:request:preference:mode'] = 'وضع الاتصال: {$a}';
$string['privacy:request:preference:textonly'] = 'نص فقط: {$a}';
$string['profile'] = 'الملف';
$string['profile_light'] = 'توفير البيانات افتراضيًا';
$string['profile_lighter'] = 'توفير البيانات ونص فقط افتراضيًا';
$string['profile_site'] = 'إعدادات الموقع';
$string['profiles'] = 'Iqralight: ملفات حسب الفئة';
$string['profiles_intro'] = 'اجعل بعض الفئات أخف من بقية الموقع، مثل مركز ريفي. لا يمكن للملف إلا أن يخفّف، ويسري أيضًا على الفئات الفرعية. يبقى اختيار المتعلّم أولًا دائمًا.';
$string['profiles_nocategory'] = 'لا توجد فئة دورات بعد.';
$string['profilessaved'] = 'تم حفظ الملفات.';
$string['progress'] = '⁦{$a->done}⁩ من ⁦{$a->total}⁩ مكتملة';
$string['progresslabel'] = 'التقدم في الدورة';
$string['region-side-pre'] = 'يمين';
$string['removeoffline'] = 'إزالة من هذا الجهاز';
$string['report_anonymous'] = 'العدّاد مجهول الهوية: تُحفظ مجاميع الموقع فقط، ولا يرى المتعلّمون مجموعًا شخصيًا.';
$string['report_cache'] = 'مقدَّم من ذاكرة الأجهزة (ميغابايت)';
$string['report_download'] = 'تنزيل التقرير';
$string['report_intro'] = 'البيانات الشبكية التي تجنّبها Iqralight على هذا الموقع، شهرًا بشهر. الفئتان منفصلتان والأرقام مقرّبة إلى الأدنى.';
$string['report_learners'] = 'المتعلّمون';
$string['report_media'] = 'وسائط غير محمَّلة (ميغابايت)';
$string['report_money'] = 'المبلغ الموفَّر (⁦{$a}⁩)';
$string['report_month'] = 'الشهر';
$string['report_title'] = 'Iqralight: البيانات التي تم تجنّبها';
$string['report_total'] = 'المجموع (ميغابايت)';
$string['resume'] = 'تابِع من حيث توقفت';
$string['resume_easy'] = 'تابِع دورتي';
$string['review'] = 'راجِع';
$string['saved'] = 'جنّبك ⁦Iqralight⁩ تنزيل ⁦{$a}⁩ ميغابايت من البيانات هذا الشهر.';
$string['saved_easy'] = 'وفّرت ⁦{$a}⁩ ميغابايت هذا الشهر.';
$string['savedata'] = 'احترام موفّر البيانات في المتصفح';
$string['savedata_desc'] = 'عند تفعيل موفّر البيانات في الهاتف، تكون الصفحة الأولى خفيفة من البداية.';
$string['savedmoney'] = 'أي ما يعادل نحو ⁦{$a->amount} {$a->currency}⁩.';
$string['serviceworker'] = 'الاحتفاظ بموارد الموقع على الأجهزة';
$string['serviceworker_desc'] = 'تُنزَّل الأنماط والبرامج النصية والأيقونات مرة واحدة لكل جهاز، وتظهر صفحة واضحة عند انقطاع الاتصال. لا تُحفظ الصفحات ولا الملفات ولا البيانات الشخصية أبدًا. ألغِ التحديد لإزالته من جميع الأجهزة عند زيارتها التالية.';
$string['setting'] = 'الإعداد';
$string['setup'] = 'Iqralight: معالج الإعداد';
$string['setup_alreadyactive'] = 'مفعّل بالفعل.';
$string['setup_colour'] = 'لونك';
$string['setup_colour_custom'] = 'اللون (⁦#RRGGBB⁩)';
$string['setup_colour_intro'] = 'اختر لون الأزرار والعناصر البارزة. جميع الألوان المقترحة تبقى مقروءة مع نص أبيض.';
$string['setup_colour_other'] = 'لون آخر';
$string['setup_completion'] = 'إتمام الأنشطة';
$string['setup_completion_desc'] = 'فعّله ليرى المتعلّمون تقدمهم وليعرف زر تابِع أين توقفوا.';
$string['setup_done'] = 'تم إعداد Iqralight.';
$string['setup_finish'] = 'إنهاء';
$string['setup_homepage'] = 'دوراتي كصفحة رئيسية';
$string['setup_homepage_desc'] = 'يصل المتعلّمون إلى دوراتي، مع بطاقة الاستئناف، فور تسجيل الدخول.';
$string['setup_logo'] = 'شعارك';
$string['setup_logo_intro'] = 'ملف PNG أو JPG لا يتجاوز 50 كيلوبايت، يظهر في صفحة تسجيل الدخول. يمكنك تخطي هذه الخطوة.';
$string['setup_native'] = 'إعدادات Moodle مفيدة';
$string['setup_native_intro'] = 'لا يغيّر Iqralight إعدادات Moodle من تلقاء نفسه أبدًا. حدّد ما تريد تفعيله.';
$string['setup_next'] = 'التالي';
$string['setup_profile'] = 'مؤسستك';
$string['setup_profile_intro'] = 'اختر الملف الأقرب إلى وضع متعلّميك. يمكنك تغييره لاحقًا في الإعدادات المسبقة.';
$string['setup_profile_keep'] = 'الإبقاء على الإعدادات الحالية';
$string['setup_step'] = 'الخطوة ⁦{$a->step}⁩ من ⁦{$a->total}⁩';
$string['start'] = 'ابدأ';
$string['stats'] = 'احتساب البيانات التي تم تجنّبها';
$string['stats_desc'] = 'يرى المتعلّمون ما جنّبهم إياه Iqralight هذا الشهر، ويحصل المسؤولون على تقرير شهري. ترسل المتصفحات أرقامها مرة كل عشر دقائق على الأكثر.';
$string['statsanonymous'] = 'عدّاد مجهول الهوية';
$string['statsanonymous_desc'] = 'الاحتفاظ بمجاميع الموقع فقط، دون أي سجل لكل متعلّم. لن يرى المتعلّمون عندها مجموعًا شخصيًا.';
$string['statsretention'] = 'الاحتفاظ بأرقام كل متعلّم لمدة (أيام)';
$string['statsretention_desc'] = 'بعد هذه المدة تُدمج في مجاميع شهرية مجهولة الهوية. بين 1 و365 يومًا.';
$string['stoplistening'] = 'إيقاف';
$string['task_aggregate_stats'] = 'دمج أرقام العدّاد القديمة في المجاميع الشهرية';
$string['textonly'] = 'نص فقط';
$string['textonly_help'] = 'تنتظر كل صورة نقرتك.';
$string['textonly_help_easy'] = 'لا صور، إلا إذا نقرت.';
$string['unreadmessages'] = '⁦{$a}⁩ محادثات غير مقروءة';
$string['verb_assign'] = 'أرسل عملي';
$string['verb_bigbluebuttonbn'] = 'انضم إلى الفصل';
$string['verb_book'] = 'اقرأ';
$string['verb_choice'] = 'أجب';
$string['verb_data'] = 'اطّلع';
$string['verb_default'] = 'افتح';
$string['verb_feedback'] = 'أجب';
$string['verb_folder'] = 'افتح المجلد';
$string['verb_forum'] = 'ناقِش';
$string['verb_glossary'] = 'اطّلع';
$string['verb_h5pactivity'] = 'أنجز النشاط';
$string['verb_imscp'] = 'اقرأ';
$string['verb_lesson'] = 'تابِع الدرس';
$string['verb_page'] = 'اقرأ';
$string['verb_quiz'] = 'أجب عن الاختبار';
$string['verb_resource'] = 'نزّل (⁦{$a}⁩)';
$string['verb_scorm'] = 'تابِع الوحدة';
$string['verb_url'] = 'افتح الرابط';
$string['verb_watch'] = 'شاهد (⁦{$a}⁩)';
$string['verb_wiki'] = 'ساهِم';
$string['verb_workshop'] = 'شارِك';
$string['wizardlinks'] = 'أعدّ Iqralight في أربع خطوات باستخدام <a href="{$a->setup}">معالج الإعداد</a>، أو طبّق <a href="{$a->presets}">إعدادًا مسبقًا</a>.';
