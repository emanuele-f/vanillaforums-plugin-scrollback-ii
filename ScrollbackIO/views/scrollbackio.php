<?php if (!defined('APPLICATION')) exit(); ?>

<?= $this->Form->Open(); ?>
<?= $this->Form->Errors(); ?>


<h1>
	<?= Gdn::Translate('Scrollback.io Integration'); ?>
</h1>

<div class="Info">
	<?= Gdn::Translate('Options to configure the scrollback.io client.'); ?>
</div>

<table class="AltRows">
	<tbody>
		<tr>
			<th>
				<?= Gdn::Translate('Room'); ?>
			</th>
			<td>
				<?= $this->Form->TextBox('Plugins.ScrollbackIO.Room', array('size'=>"80", 'placeholder' => 'vanillaforums')); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?= Gdn::Translate('Use Light Theme'); ?>
			</th>
			<td>
				<?= $this->Form->Checkbox('Plugins.ScrollbackIO.UseLightTheme'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?= Gdn::Translate('Start Opened'); ?>
			</th>
			<td>
				<?= $this->Form->Checkbox('Plugins.ScrollbackIO.StartOpen'); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?= Gdn::Translate('Scrollback Host and Port'); ?>
			</th>
			<td>
				<?= $this->Form->TextBox('Plugins.ScrollbackIO.Host', array('placeholder' => 'scrollback.io')); ?>
			</td>
		</tr>
	</tbody>
</table>

<p>
	<?= $this->Form->Close('Save'); ?>
</p>
