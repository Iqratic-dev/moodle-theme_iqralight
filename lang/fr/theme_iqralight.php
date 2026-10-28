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
 * Strings for theme_iqralight (fr).
 *
 * @package    theme_iqralight
 * @category   string
 * @copyright  2026 IQRATIC
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitynav'] = 'Navigation entre activités';
$string['advanced'] = 'Avancé';
$string['allmycourses'] = 'Voir tous mes cours';
$string['appearance'] = 'Apparence';
$string['audit'] = 'Iqralight : audit de légèreté';
$string['audit_activity'] = 'Activité';
$string['audit_block'] = 'Bloc';
$string['audit_blocks'] = 'Blocs affichés aux apprenants';
$string['audit_blocks_intro'] = 'Chaque bloc ajoute du contenu, et souvent des scripts, aux pages où il apparaît. Retirez ceux dont les apprenants n\'ont pas besoin.';
$string['audit_blocks_none'] = 'Aucun bloc n\'est affiché sur les tableaux de bord, cours ou activités des apprenants.';
$string['audit_check_cachejs'] = 'La mise en cache du JavaScript est activée.';
$string['audit_check_cachejs_fix'] = 'Activez-la dans Apparence > AJAX et Javascript.';
$string['audit_check_completion'] = 'L\'achèvement des activités est activé.';
$string['audit_check_completion_fix'] = 'Activez-le dans Fonctions avancées pour que la progression et Continuer fonctionnent.';
$string['audit_check_designermode'] = 'Le mode concepteur de thème est désactivé.';
$string['audit_check_designermode_fix'] = 'Désactivez-le dans Apparence > Thèmes > Réglages des thèmes : il désactive tous les caches.';
$string['audit_check_filter'] = 'Le filtre Iqralight est activé.';
$string['audit_check_filter_fix'] = 'Installez-le et activez-le dans Plugins > Filtres.';
$string['audit_check_filterorder'] = 'Le filtre Iqralight passe après le filtre Multimédia.';
$string['audit_check_filterorder_fix'] = 'Placez-le sous Multimédia dans Plugins > Filtres.';
$string['audit_check_passwordtoggle'] = 'Les apprenants peuvent afficher leur mot de passe pendant la saisie.';
$string['audit_check_passwordtoggle_fix'] = 'Activez « Afficher le mot de passe » dans Plugins > Authentification > Gestion de l\'authentification.';
$string['audit_check_serviceworker'] = 'Le service worker est activé.';
$string['audit_check_serviceworker_fix'] = 'Cochez-le dans les réglages d\'allègement d\'Iqralight, sauf si votre politique informatique l\'interdit.';
$string['audit_check_slasharguments'] = 'Les adresses de fichiers utilisent les arguments barre oblique.';
$string['audit_check_slasharguments_fix'] = 'Activez slasharguments dans Serveur > HTTP : les polices et le cache d\'Iqralight en ont besoin.';
$string['audit_checks'] = 'Liste de vérification des réglages';
$string['audit_component'] = 'Composant';
$string['audit_course'] = 'Cours';
$string['audit_dryrun'] = 'Clic pour charger : mode à blanc';
$string['audit_dryrun_intro'] = 'Choisissez un cours pour voir ce que le filtre Iqralight ferait attendre un appui. Rien n\'est modifié.';
$string['audit_dryrun_nofilter'] = 'Installez le filtre Iqralight pour utiliser ce mode à blanc.';
$string['audit_dryrun_nothing'] = 'Rien dans ce cours n\'attendrait un appui.';
$string['audit_file'] = 'Fichier';
$string['audit_files'] = 'Fichiers de cours les plus lourds';
$string['audit_files_none'] = 'Aucun fichier de cours ne dépasse 100 Ko.';
$string['audit_filesize'] = 'Taille';
$string['audit_firstvisit'] = 'Première visite d\'un apprenant';
$string['audit_firstvisit_note'] = 'Estimation hors page elle-même (20 à 30 Ko) et hors contenus de cours. Les visites suivantes réutilisent ces fichiers depuis le cache de l\'appareil.';
$string['audit_icons'] = 'Icônes absentes des polices allégées';
$string['audit_icons_none'] = 'Toutes les icônes utilisées sur ce site sont dans les polices allégées.';
$string['audit_icons_some'] = '{$a} icônes manquent dans les polices allégées : sur les pages qui les affichent, le navigateur télécharge la police complète. Régénérez les polices avec cli/build_icon_fonts.py.';
$string['audit_instances'] = 'Occurrences';
$string['audit_intro'] = 'Ce qui pèse sur la connexion de vos apprenants sur ce site, mesuré à partir des fichiers que Moodle sert. Rien n\'est modifié ici.';
$string['audit_javascript'] = 'Scripts par plugin';
$string['audit_javascript_advice'] = 'Désinstaller les plugins que personne n\'utilise allège ce script pour chaque apprenant. C\'est votre décision : passez-les en revue dans la <a href="{$a}">vue d\'ensemble des plugins</a>.';
$string['audit_javascript_intro'] = '{$a->count} composants ajoutent des scripts : {$a->raw} au total, {$a->compressed} une fois compressés, téléchargés par chaque nouveau visiteur.';
$string['audit_kind'] = 'Contenu';
$string['audit_kind_document'] = 'Document';
$string['audit_kind_image'] = 'Image';
$string['audit_kind_video'] = 'Vidéo';
$string['audit_part'] = 'Élément';
$string['audit_part_amd'] = 'Scripts Moodle (tous les plugins installés)';
$string['audit_part_css'] = 'Feuille de style';
$string['audit_part_fonts'] = 'Polices d\'icônes (allégées)';
$string['audit_part_otherjs'] = 'Autres scripts du core';
$string['audit_size'] = 'Taille (compressée)';
$string['audit_standard'] = 'Moodle standard';
$string['audit_thirdparty'] = 'Plugin tiers';
$string['audit_total'] = 'Total';
$string['audit_type'] = 'Type';
$string['autotextonly'] = 'Texte seul sur les connexions très lentes';
$string['autotextonly_desc'] = 'Sur une connexion très lente (2G), les images attendent un appui même si elles sont légères. Nécessite le filtre Iqralight.';
$string['backtocourse'] = 'Retour au cours';
$string['brandcolor'] = 'Couleur de marque';
$string['brandcolor_contrast'] = 'Un texte blanc sur cette couleur est difficile à lire (contraste de {$a->ratio}:1, il faut au moins 4,5:1). Essayez {$a->suggestion}, une nuance plus foncée de la même couleur.';
$string['brandcolor_desc'] = 'La couleur des boutons, des liens et des éléments mis en avant. Iqralight en déduit toutes les autres nuances et vérifie qu\'un texte blanc reste lisible dessus.';
$string['brandcolor_format'] = 'Saisissez une couleur au format #RRGGBB, par exemple #0B6E5A.';
$string['chapter'] = 'Chapitre {$a}';
$string['choosereadme'] = 'Iqralight est un thème Moodle pour les apprenants dont la connexion est très faible, coûteuse ou instable : simple, moderne et économe en données.';
$string['cli_dryrun'] = 'Simulation : rien n\'a été modifié.';
$string['cli_help'] = 'Applique un préréglage Iqralight.

Options :
-l, --list         Lister les préréglages fournis
-p, --preset=NOM   Appliquer un préréglage fourni
-f, --file=CHEMIN  Appliquer une configuration exportée
-n, --dry-run      Afficher les changements sans les appliquer
-h, --help         Afficher cette aide';
$string['cli_unreadable'] = 'Impossible de lire {$a}.';
$string['configtitle'] = 'Iqralight';
$string['continue'] = 'Continuer';
$string['contrast'] = 'Contraste élevé';
$string['contrast_help'] = 'Texte noir, bordures épaisses, liens soulignés.';
$string['counter'] = 'Compteur de données';
$string['coursereport'] = 'Légèreté du cours';
$string['coursereport_advice'] = 'Comment alléger le cours';
$string['coursereport_advice_images'] = 'Gardez les images sous 300 Ko : une largeur de 1 200 pixels suffit pour un téléphone.';
$string['coursereport_advice_pdf'] = 'Compressez les PDF avant de les déposer : un document scanné peut souvent être divisé par dix.';
$string['coursereport_advice_text'] = 'Écrivez l\'essentiel en texte dans une page : le texte ne pèse presque rien et peut être lu à voix haute.';
$string['coursereport_advice_video'] = 'Hébergez les vidéos sur une plateforme vidéo et faites-y un lien, plutôt que de les déposer dans le cours.';
$string['coursereport_files'] = 'Fichiers les plus lourds du cours';
$string['coursereport_intro'] = 'Ce qui pèse sur la connexion de vos apprenants dans ce cours, et comment l\'alléger. Rien n\'est modifié ici.';
$string['coursereport_waiting'] = 'Ce qui attend un appui en mode Économie';
$string['credit'] = 'Iqralight est conçu et maintenu par <a href="{$a}">IQRATIC</a>, pour les établissements qui forment des apprenants à faible connectivité : déploiement, formation des enseignants et accompagnement.';
$string['currentvalue'] = 'Valeur actuelle';
$string['darkmode'] = 'Mode sombre';
$string['darkmode_auto'] = 'Automatique : suit le réglage de l\'appareil';
$string['darkmode_desc'] = 'S\'applique aux pages apprenant vérifiées en sombre : Mes cours, pages de cours, page, forum, devoir, accueil des quiz, livre, messages, profil, notes, calendrier, préférences et connexion. Toutes les autres pages restent claires, et les contenus des enseignants gardent une surface claire pour que leurs couleurs restent lisibles.';
$string['darkmode_off'] = 'Désactivé';
$string['darkmode_on'] = 'Toujours';
$string['dataavoided'] = 'Données évitées';
$string['defaultmode'] = 'Mode de connexion par défaut';
$string['defaultmode_desc'] = 'S\'applique tant que l\'apprenant n\'a pas choisi son propre mode. Automatique détecte la qualité de la connexion sur chaque appareil.';
$string['display'] = 'Affichage';
$string['done'] = 'Terminé';
$string['easyread'] = 'Lecture facile';
$string['easyread_help'] = 'Texte et icônes plus grands, et un bouton Écouter qui lit la page à voix haute.';
$string['exportconfig'] = 'Exporter la configuration';
$string['exportimport'] = 'Export et import';
$string['greeting'] = 'Bonjour, {$a}';
$string['importfile'] = 'Fichier de configuration (JSON)';
$string['importpreview'] = 'Prévisualiser les changements';
$string['keepoffline'] = 'Garder hors ligne';
$string['lightness'] = 'Allègement';
$string['listen'] = 'Écouter';
$string['logos'] = 'Logo';
$string['logos_desc'] = 'Iqralight utilise le logo du site défini dans <a href="{$a}">Apparence &gt; Logos</a> (PNG ou JPG). Gardez-le sous 50 Ko : l\'assistant de configuration refuse les fichiers plus lourds.';
$string['logotoolarge'] = 'Le logo pèse {$a} : 50 Ko au maximum sont acceptés.';
$string['mbcurrency'] = 'Devise';
$string['mbcurrency_desc'] = 'Affichée après le montant, par exemple FC, MAD ou USD.';
$string['mbprice'] = 'Prix local de 1 Mo de données mobiles';
$string['mbprice_desc'] = 'Facultatif. Renseigné avec une devise, les apprenants et le rapport voient aussi l\'équivalent en argent. Utilisez un point pour les décimales.';
$string['mode_auto'] = 'Automatique';
$string['mode_auto_help'] = 'Iqralight s\'adapte à votre connexion.';
$string['mode_auto_help_easy'] = 'Iqralight choisit pour vous.';
$string['mode_economy'] = 'Économie de données';
$string['mode_economy_help'] = 'Les images et contenus lourds attendent votre accord.';
$string['mode_economy_help_easy'] = 'Les images attendent votre appui.';
$string['mode_full'] = 'Tout charger';
$string['mode_full_help'] = 'Tout se charge immédiatement.';
$string['mode_full_help_easy'] = 'Tout s\'affiche tout de suite.';
$string['modechip_economy'] = 'Économie';
$string['modechip_full'] = 'Tout charger';
$string['modeintro'] = 'Choisissez comment Iqralight utilise vos données sur cet appareil et sur ceux où vous vous connectez.';
$string['modeintro_easy'] = 'Choisissez comment les pages se chargent.';
$string['modetitle'] = 'Mode de connexion';
$string['mycourses'] = 'Mes cours';
$string['nav_account'] = 'Mon compte';
$string['nav_courses'] = 'Mes cours';
$string['nav_label'] = 'Navigation principale';
$string['nav_messages'] = 'Messages';
$string['newvalue'] = 'Nouvelle valeur';
$string['nextactivity'] = 'Suivant : {$a}';
$string['offline_kept'] = 'Disponible sur cet appareil';
$string['offline_message'] = 'La page se rechargera dès le retour de la connexion.';
$string['offline_none'] = 'Rien n\'a été gardé sur cet appareil.';
$string['offline_retry'] = 'Réessayer';
$string['offline_title'] = 'Connexion perdue';
$string['offlineavailable'] = 'Disponible hors ligne sur cet appareil.';
$string['offlinefailed'] = 'Cette ressource n\'a pas pu être gardée. Réessayez avec une meilleure connexion.';
$string['offlinefailed_easy'] = 'Ça n\'a pas marché. Réessayez avec un meilleur réseau.';
$string['offlinefull'] = 'Plus assez de place : {$a} Mo au plus peuvent être gardés sur cet appareil. Retirez d\'abord une ressource gardée.';
$string['offlinefull_easy'] = 'Plus de place sur ce téléphone. Retirez d\'abord une leçon gardée.';
$string['offlinequota'] = 'Place hors ligne par appareil (Mo)';
$string['offlinequota_desc'] = 'Entre 10 et 200 Mo.';
$string['offlinereading'] = 'Lecture hors ligne';
$string['offlinereading_desc'] = 'Les apprenants peuvent garder des pages, chapitres de livre et PDF sur leur appareil et les lire sans réseau. Les ressources gardées sont effacées à la déconnexion, quand une autre personne se connecte sur l\'appareil, et après 30 jours sans lecture. À laisser désactivé sur les postes partagés. Nécessite le service worker.';
$string['offlinesaving'] = 'Enregistrement…';
$string['offlinesubmit'] = 'Pas de connexion : votre envoi n\'est pas parti. Ce que vous avez écrit est conservé ici ; renvoyez-le dès le retour du réseau.';
$string['offlinesubmit_easy'] = 'Pas de réseau. Votre texte est gardé. Renvoyez-le plus tard.';
$string['opencourse'] = 'Ouvrir le cours';
$string['percentdone'] = '{$a} %';
$string['pluginname'] = 'Iqralight';
$string['preset_prudent'] = 'Prudent';
$string['preset_prudent_desc'] = 'Sans service worker ; compteur anonyme. Pour un premier essai ou une politique informatique stricte.';
$string['preset_rural'] = 'Établissement rural';
$string['preset_rural_desc'] = 'Économie de données par défaut, texte seul en 2G, images de plus de 200 Ko à la demande.';
$string['preset_sharedroom'] = 'Salle informatique partagée';
$string['preset_sharedroom_desc'] = 'Mode automatique, compteur anonyme : plusieurs personnes utilisent les mêmes postes.';
$string['preset_university'] = 'Université à faible bande passante';
$string['preset_university_desc'] = 'Mode automatique, texte seul en 2G, images de plus de 500 Ko à la demande.';
$string['presetapplied'] = 'Le préréglage a été appliqué.';
$string['presetapply'] = 'Appliquer ces changements';
$string['preseterror_format'] = 'Ce fichier n\'est pas une configuration Iqralight.';
$string['preseterror_logo'] = 'Le logo de ce fichier n\'est pas une image PNG ou JPG valide.';
$string['preseterror_unknown'] = 'Réglage inconnu : {$a}.';
$string['preseterror_value'] = 'Valeur invalide pour {$a}.';
$string['presetnochange'] = 'Cette configuration est déjà en place : rien ne changerait.';
$string['presetpreview'] = 'Changements qui seront appliqués';
$string['presetpreviewlink'] = 'Aperçu';
$string['presets'] = 'Iqralight : préréglages';
$string['presets_intro'] = 'Appliquez une configuration adaptée à votre établissement. Chaque changement est affiché avant d\'être appliqué.';
$string['presetunknown'] = 'Préréglage inconnu : {$a}.';
$string['previousactivity'] = 'Précédent';
$string['privacy:metadata:preference:contrast'] = 'Si l\'apprenant a choisi l\'affichage à contraste élevé.';
$string['privacy:metadata:preference:easyread'] = 'Si l\'apprenant a choisi l\'affichage Lecture facile.';
$string['privacy:metadata:preference:mode'] = 'Le mode de connexion choisi par l\'apprenant : automatique, économie de données ou tout charger.';
$string['privacy:metadata:preference:textonly'] = 'Si l\'apprenant a choisi d\'afficher le texte seul, les images se chargeant d\'un appui.';
$string['privacy:metadata:stats'] = 'Données réseau évitées par chaque apprenant et par jour, conservées pour une durée limitée puis fondues dans des totaux mensuels anonymes.';
$string['privacy:metadata:stats:cachebytes'] = 'Octets servis par le cache de l\'appareil au lieu du réseau.';
$string['privacy:metadata:stats:day'] = 'Le jour concerné.';
$string['privacy:metadata:stats:mediabytes'] = 'Octets des médias que l\'apprenant n\'a pas chargés.';
$string['privacy:metadata:stats:userid'] = 'L\'apprenant.';
$string['privacy:request:preference:contrast'] = 'Contraste élevé : {$a}';
$string['privacy:request:preference:easyread'] = 'Lecture facile : {$a}';
$string['privacy:request:preference:mode'] = 'Mode de connexion : {$a}';
$string['privacy:request:preference:textonly'] = 'Texte seul : {$a}';
$string['profile'] = 'Profil';
$string['profile_light'] = 'Économie de données par défaut';
$string['profile_lighter'] = 'Économie de données et texte seul par défaut';
$string['profile_site'] = 'Réglages du site';
$string['profiles'] = 'Iqralight : profils par catégorie';
$string['profiles_intro'] = 'Rendez certaines catégories plus économes que le reste du site, par exemple un centre rural. Un profil ne peut qu\'alléger, et s\'applique aussi aux sous-catégories. Le choix de l\'apprenant reste toujours prioritaire.';
$string['profiles_nocategory'] = 'Il n\'y a pas encore de catégorie de cours.';
$string['profilessaved'] = 'Les profils ont été enregistrés.';
$string['progress'] = '{$a->done} sur {$a->total} terminés';
$string['progresslabel'] = 'Progression dans le cours';
$string['rawscss'] = 'SCSS brut';
$string['rawscss_desc'] = 'Règles SCSS ou CSS, ajoutées après celles du thème. Restez léger : chaque ligne est envoyée à chaque apprenant.';
$string['rawscsspre'] = 'SCSS initial brut';
$string['rawscsspre_desc'] = 'Variables SCSS, compilées avant le thème. Utilisez ce champ pour modifier une variable, par exemple $border-radius: 0.5rem;';
$string['region-side-pre'] = 'Droite';
$string['removeoffline'] = 'Retirer de cet appareil';
$string['report_anonymous'] = 'Le compteur est anonyme : seuls les totaux du site sont conservés, les apprenants ne voient pas de total personnel.';
$string['report_cache'] = 'Servi par le cache des appareils (Mo)';
$string['report_download'] = 'Télécharger le rapport';
$string['report_intro'] = 'Données réseau évitées par Iqralight sur ce site, mois par mois. Les deux catégories sont séparées et les chiffres sont arrondis à l\'inférieur.';
$string['report_learners'] = 'Apprenants';
$string['report_media'] = 'Médias non chargés (Mo)';
$string['report_money'] = 'Argent évité ({$a})';
$string['report_month'] = 'Mois';
$string['report_title'] = 'Iqralight : données évitées';
$string['report_total'] = 'Total (Mo)';
$string['resume'] = 'Reprendre là où vous en étiez';
$string['resume_easy'] = 'Continuer mon cours';
$string['review'] = 'Revoir';
$string['saved'] = 'Iqralight vous a évité {$a} Mo de données ce mois-ci.';
$string['saved_easy'] = '{$a} Mo économisés ce mois-ci.';
$string['savedata'] = 'Respecter l\'économiseur de données du navigateur';
$string['savedata_desc'] = 'Quand l\'économiseur de données du téléphone est actif, la toute première page est déjà légère.';
$string['savedmoney'] = 'Soit environ {$a->amount} {$a->currency}.';
$string['serviceworker'] = 'Garder les ressources du site sur les appareils';
$string['serviceworker_desc'] = 'Les styles, scripts et icônes ne sont téléchargés qu\'une fois par appareil, même si le navigateur vide son cache, et une page claire s\'affiche quand la connexion tombe. Les pages, fichiers et données personnelles ne sont jamais conservés. Décochez pour le retirer de tous les appareils à leur prochaine visite.';
$string['setting'] = 'Réglage';
$string['setup'] = 'Iqralight : assistant de configuration';
$string['setup_alreadyactive'] = 'Déjà actif.';
$string['setup_colour'] = 'Votre couleur';
$string['setup_colour_custom'] = 'Couleur (#RRGGBB)';
$string['setup_colour_intro'] = 'Choisissez la couleur des boutons et des éléments mis en avant. Toutes les couleurs proposées restent lisibles avec un texte blanc.';
$string['setup_colour_other'] = 'Une autre couleur';
$string['setup_completion'] = 'Achèvement des activités';
$string['setup_completion_desc'] = 'Activez-le pour que les apprenants voient leur progression et que le bouton Continuer sache où ils se sont arrêtés.';
$string['setup_done'] = 'Iqralight est configuré.';
$string['setup_finish'] = 'Terminer';
$string['setup_homepage'] = 'Mes cours comme page d\'accueil';
$string['setup_homepage_desc'] = 'Les apprenants arrivent sur Mes cours, avec la carte Reprendre, dès leur connexion.';
$string['setup_logo'] = 'Votre logo';
$string['setup_logo_intro'] = 'Un fichier PNG ou JPG de 50 Ko au maximum, affiché sur la page de connexion. Vous pouvez passer cette étape.';
$string['setup_native'] = 'Réglages Moodle utiles';
$string['setup_native_intro'] = 'Iqralight ne modifie jamais lui-même les réglages de Moodle. Cochez ceux que vous souhaitez activer.';
$string['setup_next'] = 'Suivant';
$string['setup_profile'] = 'Votre établissement';
$string['setup_profile_intro'] = 'Choisissez le profil le plus proche de la situation de vos apprenants. Vous pourrez le changer plus tard dans Préréglages.';
$string['setup_profile_keep'] = 'Garder les réglages actuels';
$string['setup_step'] = 'Étape {$a->step} sur {$a->total}';
$string['start'] = 'Commencer';
$string['stats'] = 'Compter les données évitées';
$string['stats_desc'] = 'Les apprenants voient ce qu\'Iqralight leur a évité ce mois-ci, et les administrateurs disposent d\'un rapport mensuel. Les navigateurs envoient leurs chiffres au plus toutes les dix minutes.';
$string['statsanonymous'] = 'Compteur anonyme';
$string['statsanonymous_desc'] = 'Ne conserver que les totaux du site, sans aucune ligne par apprenant. Les apprenants ne voient alors pas de total personnel.';
$string['statsretention'] = 'Conserver les chiffres par apprenant pendant (jours)';
$string['statsretention_desc'] = 'Passé ce délai, ils sont fondus dans des totaux mensuels anonymes. Entre 1 et 365 jours.';
$string['stoplistening'] = 'Arrêter';
$string['task_aggregate_stats'] = 'Fondre les anciens chiffres du compteur dans les totaux mensuels';
$string['textonly'] = 'Texte seul';
$string['textonly_help'] = 'Chaque image attend votre appui.';
$string['textonly_help_easy'] = 'Pas d\'images, sauf si vous appuyez.';
$string['unreadmessages'] = '{$a} conversations non lues';
$string['verb_assign'] = 'Envoyer mon travail';
$string['verb_bigbluebuttonbn'] = 'Rejoindre la classe';
$string['verb_book'] = 'Lire';
$string['verb_choice'] = 'Répondre';
$string['verb_data'] = 'Consulter';
$string['verb_default'] = 'Ouvrir';
$string['verb_feedback'] = 'Répondre';
$string['verb_folder'] = 'Ouvrir le dossier';
$string['verb_forum'] = 'Discuter';
$string['verb_glossary'] = 'Consulter';
$string['verb_h5pactivity'] = 'Faire l\'activité';
$string['verb_imscp'] = 'Lire';
$string['verb_lesson'] = 'Suivre la leçon';
$string['verb_page'] = 'Lire';
$string['verb_quiz'] = 'Répondre au quiz';
$string['verb_resource'] = 'Télécharger ({$a})';
$string['verb_scorm'] = 'Suivre le module';
$string['verb_url'] = 'Ouvrir le lien';
$string['verb_watch'] = 'Regarder ({$a})';
$string['verb_wiki'] = 'Contribuer';
$string['verb_workshop'] = 'Participer';
$string['wizardlinks'] = 'Configurez Iqralight en quatre étapes avec l\'<a href="{$a->setup}">assistant de configuration</a>, ou appliquez un <a href="{$a->presets}">préréglage</a>.';
