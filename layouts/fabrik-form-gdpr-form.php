<?php
defined('JPATH_BASE') or die;
$d = $displayData;

?>
<div class="gdprError alert alert-error <?php echo $d->errClass ?>">
	<button class="close" data-dismiss="alert">×</button>
	<?php echo $d->errText; ?>
</div>

<?php
if($d->useFieldset) :
?>
<fieldset class="<?php echo $d->fieldsetClass; ?>">
	<legend class="<?php echo $d->legendClass; ?>">
		<?php echo $d->legendText; ?>
	</legend>
<?php
endif;

if ($d->showConsent) :
?>
	<div class="contact_consent">
		<input id="fabrik_contact_consent" type="checkbox" name="fabrik_contact_consent" value="1" style="margin-right: 10px;">
		<label for="fabrik_contact_consent">
			<?php echo $d->consentText; ?>
		</label>
	</div>
<?php
endif;

if($d->showMailing) :
?>
	<div class="acymailing_consent">
		<input id="fabrik_acymailing_signup" type="checkbox" name="fabrik_acymailing_signup" value="1" style="margin-right: 10px;">
		<label for="fabrik_acymailing_signup">
			<?php echo $d->mailingText; ?>
		</label>
	</div>
<?php
endif;

if($d->useFieldset) :
?>
</fieldset>
<?php
endif;