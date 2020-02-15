<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2012		JF FERRY			<jfefe@aternatik.fr>
 * Copyright (C) 2017		Eric GROULT			<eric@code42.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *   	\file       event/index.php
 *		\ingroup    event
 *		\brief      Index page of module event
 */

// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (! $res && file_exists("../../main.inc.php")) $res=include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=include("../../../main.inc.php"); // for curstom directory

if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/doleditor.class.php");
require_once("../class/event.class.php");
require_once("../class/eventlevel.class.php");
require_once("../class/day.class.php");
require_once("../lib/event.lib.php");

// Load traductions files required by by page
$langs->loadLangs(array("admin","companies","other","event@event","cron"));

// Get parameters
$id			= GETPOST('id','int');
$dayid		= GETPOST('dayid','int');
$action		= GETPOST('action','alpha');
$value 		= GETPOST('value','alpha');

// Protection if external user or not an admin
if ($user->societe_id > 0 || !$user->rights->event->setup_text)
{
	accessforbidden();
}

/*
 * Actions
*/
if ($action == 'setvar')
{
	$event_participate_message=GETPOST('EVENT_PARTICIPATE_MESSAGE');
	dolibarr_set_const($db, "EVENT_PARTICIPATE_MESSAGE",dol_htmlcleanlastbr($event_participate_message),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	$event_participate_sujet=GETPOST('EVENT_PARTICIPATE_SUJET');
	dolibarr_set_const($db, "EVENT_PARTICIPATE_SUJET",dol_htmlcleanlastbr($event_participate_sujet),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	$event_not_participate_message=GETPOST('EVENT_NOT_PARTICIPATE_MESSAGE');
	dolibarr_set_const($db, "EVENT_NOT_PARTICIPATE_MESSAGE",dol_htmlcleanlastbr($event_not_participate_message),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	$event_not_participate_sujet=GETPOST('EVENT_NOT_PARTICIPATE_SUJET');
	dolibarr_set_const($db, "EVENT_NOT_PARTICIPATE_SUJET",dol_htmlcleanlastbr($event_not_participate_sujet),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	// Event Relance Waining Sujet
	$event_relance_waiting_sujet=GETPOST('EVENT_RELANCE_WAITING_SUJET');
	dolibarr_set_const($db, "EVENT_RELANCE_WAITING_SUJET",dol_htmlcleanlastbr($event_relance_waiting_sujet),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	// Event Relance Wainting Message
	$event_relance_waiting_message=GETPOST('EVENT_RELANCE_WAITING_MESSAGE');
	dolibarr_set_const($db, "EVENT_RELANCE_WAITING_MESSAGE",dol_htmlcleanlastbr($event_relance_waiting_message),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	// Event Relance Confirm Sujet
	$event_relance_confirm_sujet=GETPOST('EVENT_RELANCE_CONFIRM_SUJET');
	dolibarr_set_const($db, "EVENT_RELANCE_CONFIRM_SUJET",dol_htmlcleanlastbr($event_relance_confirm_sujet),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	// Event Relance Confirm Message
	$event_relance_confirm_message=GETPOST('EVENT_RELANCE_CONFIRM_MESSAGE');
	dolibarr_set_const($db, "EVENT_RELANCE_CONFIRM_MESSAGE",dol_htmlcleanlastbr($event_relance_confirm_message),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	
	
	// Event Canceled Sujet
	$event_canceled_sujet=GETPOST('EVENT_CANCELED_SUJET');
	dolibarr_set_const($db, "EVENT_CANCELED_SUJET",dol_htmlcleanlastbr($event_canceled_sujet),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;	
	
	// Event Canceled Message
	$event_canceled_message=GETPOST('EVENT_CANCELED_MESSAGE');
	dolibarr_set_const($db, "EVENT_CANCELED_MESSAGE",dol_htmlcleanlastbr($event_canceled_message),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;	

	// Signature
	$event_registration_sign_email=GETPOST('EVENT_REGISTRATION_SIGN_EMAIL');
	dolibarr_set_const($db, "EVENT_REGISTRATION_SIGN_EMAIL",dol_htmlcleanlastbr($event_registration_sign_email),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	// Test error
	if (! $error)
	{
		$mesg = "<div class=\"ok\">".$langs->trans("SetupSaved")."</div>";
	}
	else
	{
		$mesg = "<div class=\"error\">".$langs->trans("Error")."</div>";
	}
}

/***************************************************
 * View
****************************************************/

llxHeader('',$langs->trans("EventSetupPageStyle"),'');

print_fiche_titre($langs->trans("EventSetupPageStyle"),$linkback,'setup');

// Configuration header
$head = event_admin_text_prepare_head();
dol_fiche_head($head, 'EventSetupTextMail', $langs->trans('EventSetupStyle'), 0, 'event@event');

$form=new Form($db);

$dirmodels=array_merge(array('/'),(array) $conf->modules_parts['models']);

dol_htmloutput_mesg($mesg,$mesgs);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvar">';

/*
 * GESTION DES TEXTES
*/

print load_fiche_titre($langs->trans('Admin_menu_manage_content_add'));
print '<div class="div-table-responsive">';
print "\n".'<table class="noborder" width=100%>';

// Mail Participate
print '<tr class="liste_titre oddeven">';
print '<td colspan="4">'.$langs->trans("MailParticipate").'</td>';
print '</tr>';

// Participate Subject
print '<tr><td width="35%">'.$langs->trans("ParticipateSubject").'</td><td colspan="2">';
print '<input type="text" name="EVENT_PARTICIPATE_SUJET" value="'.$conf->global->EVENT_PARTICIPATE_SUJET.'" size="50" maxlength="70">';
print '</td></tr>'."\n";

// Participate Message
print '<tr><td width="35%">'.$langs->trans("ParticipateMessage").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_PARTICIPATE_MESSAGE', (isset($conf->global->EVENT_PARTICIPATE_MESSAGE)?$conf->global->EVENT_PARTICIPATE_MESSAGE:''), '', 142, 'dolibarr_emailing', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";

// Mail Not Participate
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("MailNotParticipate").'</td>';
print '</tr>';

// Not Participate Sujet
print '<tr><td width="35%">'.$langs->trans("NotParticipateSujet").'</td><td colspan="2">';
print '<input type="text" name="EVENT_NOT_PARTICIPATE_SUJET" value="'.$conf->global->EVENT_NOT_PARTICIPATE_SUJET.'" size="50" maxlength="70">';
print '</td></tr>'."\n";

// Not Participate Message
print '<tr><td width="35%">'.$langs->trans("NotParticipateMessage").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_NOT_PARTICIPATE_MESSAGE', (isset($conf->global->EVENT_NOT_PARTICIPATE_MESSAGE)?$conf->global->EVENT_NOT_PARTICIPATE_MESSAGE:''), '', 142, 'dolibarr_emailing', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";

// Mail Relance Waiting
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("MailRelanceWaiting").'</td>';
print '</tr>';

// Relance Waiting Sujet
print '<tr><td width="35%">'.$langs->trans("RelanceWaitingSujet").'</td><td colspan="2">';
print '<input type="text" name="EVENT_RELANCE_WAITING_SUJET" value="'.$conf->global->EVENT_RELANCE_WAITING_SUJET.'" size="50" maxlength="70">';
print '</td></tr>'."\n";

// Relance Waiting Message
print '<tr><td width="35%">'.$langs->trans("RelanceWaitingMessage").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_RELANCE_WAITING_MESSAGE', (isset($conf->global->EVENT_RELANCE_WAITING_MESSAGE)?$conf->global->EVENT_RELANCE_WAITING_MESSAGE:''), '', 142, 'dolibarr_emailing', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";

// Mail Relance Confirmed
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("MailRelanceConfirmed").'</td>';
print '</tr>';

// Relance Confirmed Sujet
print '<tr><td width="35%">'.$langs->trans("RelanceConfirmedSujet").'</td><td colspan="2">';
print '<input type="text" name="EVENT_RELANCE_CONFIRM_SUJET" value="'.$conf->global->EVENT_RELANCE_CONFIRM_SUJET.'" size="50" maxlength="70">';
print '</td></tr>'."\n";

// Relance Confirmed Message
print '<tr><td width="35%">'.$langs->trans("RelanceConfirmedMessage").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_RELANCE_CONFIRM_MESSAGE', (isset($conf->global->EVENT_RELANCE_CONFIRM_MESSAGE)?$conf->global->EVENT_RELANCE_CONFIRM_MESSAGE:''), '', 142, 'dolibarr_emailing', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";


/// AJOUT LAURENT /////////////////////////////

// Mail Canceled
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("MailCanceled").'</td>';
print '</tr>';

// Relance Confirmed Sujet
print '<tr><td width="35%">'.$langs->trans("CanceledSujet").'</td><td colspan="2">';
print '<input type="text" name="EVENT_CANCELED_SUJET" value="'.$conf->global->EVENT_CANCELED_SUJET.'" size="50" maxlength="70">';
print '</td></tr>'."\n";

// Relance Confirmed Message
print '<tr><td width="35%">'.$langs->trans("CanceledMessage").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_CANCELED_MESSAGE', (isset($conf->global->EVENT_CANCELED_MESSAGE)?$conf->global->EVENT_CANCELED_MESSAGE:''), '', 142, 'dolibarr_emailing', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";



/////////////////////////////////////////////////


// Signature
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("Signature").'</td>';
print '</tr>';

// Text to add after email content - Signature
print '<tr><td width="35%">'.$langs->trans("EventTextToAddEmails").'</td><td colspan="2">';
$doleditor = new DolEditor('EVENT_REGISTRATION_SIGN_EMAIL', (isset($conf->global->EVENT_REGISTRATION_SIGN_EMAIL)?$conf->global->EVENT_REGISTRATION_SIGN_EMAIL:''), '', 142, 'event_registration_sign_email', 'In', true, true, true, ROWS_4, 90);
$doleditor->Create();
print '</td></tr>'."\n";

print '</table>';
print '</div>';
dol_fiche_end();

print '<div class="center">';
print '<input type="submit" class="button hideifnotset" value="'.$langs->trans("Save").'">';
print '</div>';

print "</form>\n";
print '<br />';

// End of page
llxFooter();
$db->close();
