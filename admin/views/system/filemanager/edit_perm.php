<form action="<?php echo base_url($this->config->config['index_url'].'sys_filemanager.html');?>" method="get" id="fileForm">
<table class="table_add">
	<tr>
		<td class="width tright">权限:</td>
		<td>
			<input type="text" id="file_perm" name="perm" class="input" style="width: 80%;" maxlength="4" />
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" id="fileSub" value="编辑" />
			<input type="hidden" id="file_path" name="path">
			<input type="hidden" id="file_editor" name="editor">
			<input type="hidden" name="action" value="editperm">
		</td>
	</tr>
</table>
</form>