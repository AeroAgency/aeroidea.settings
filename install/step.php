<? use Bitrix\Main\Localization\Loc;

if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote(Loc::getMessage("MSG_INSTALLED"));
?>