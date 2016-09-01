<?php
	$id = @$_GET['id'];
	if(@$_POST['keymap'] != '') {
		if($id=='')
			$id = uniqid();
		file_put_contents("data/$id", $_POST['keymap'].' '.$_POST['language'].','.$_POST['code']);
		$url = $_SERVER['HTTP_HOST'];
		$path = rtrim(dirname($_SERVER['PHP_SELF']), '\\/');
		header("Location:http://$url$path/$id");
		exit();
	}

	if($id=='') {
		$keymap = '';
		$language = 'Normal';
		$code = '';
	} else if(file_exists("data/$id")) {
		$data = file_get_contents("data/$id");
		$info = strstr($data, ',', true);
		$code = substr(strstr($data, ','), 1);
		sscanf($info, "%s %s", $keymap, $language);
	} else {
		$url = $_SERVER['HTTP_HOST'];
		$path = trim(dirname($_SERVER['PHP_SELF']), '\\/');
		header("Location:http://$url/$path/index.php");
		exit();
	}

	$lang_info = array(
		'Normal'     => [ '', ['normal'] ],
		'C'          => [ 'text/x-csrc', ['clike'] ],
		'C++'        => [ 'text/x-c++src', ['clike'] ],
		'JAVA'       => [ 'text/x-java', ['clike'] ],
		'HTML'	     => [ 'text/html', ['xml'] ],
		'CSS'	     => [ 'text/css', ['css'] ],
		'JavaScript' => [ 'text/javascript', ['javascript'] ],
		'Python'     => [ 'text/x-python', ['python'] ],
		'PHP'        => [ 'text/x-php', ['clike', 'php'] ],
		'Pascal'     => [ 'text/x-pascal', ['pascal'] ],
		'Perl'       => [ 'text/x-perl', ['perl'] ],
		'Ruby'       => [ 'text/x-ruby', ['ruby'] ],
		'SQL'        => [ 'text/x-sql', ['sql'] ],
		'Shell'      => [ 'text/x-sh', ['shell'] ],
	);
	$lang = $lang_info[$language];
 ?>
<!DOCTYPE html>
<html>
<head>
	<script src="lib/codemirror.js"></script>
	<link rel="stylesheet" href="lib/codemirror.css">
	<link rel="stylesheet" href="addon/dialog/dialog.css">
	<?php
		foreach($lang[1] as $value)
			echo '<script src="mode/'.$value.'/'.$value.'.js"></script>';
	?>
	<script src="addon/edit/matchbrackets.js"></script>
	<script src="addon/dialog/dialog.js"></script>
	<script src="addon/search/searchcursor.js"></script>
	<?php if($keymap == 'vim') { ?>
	<script src="keymap/vim.js"></script>
	<?php } else if($keymap == 'emacs') { ?>
	<script src="keymap/emacs.js"></script>
	<?php } ?>
	<style>
		.CodeMirror {
			height: 600px;
			border: 1px solid #eee;
			font-size: 18px;
			font-weight: bold;
			font-family: Consolas;
		}
		.container {
			margin: 0px auto;
			width: 80%;
		}
	</style>
</head>
<body>
<div class="container">
	<form id="content" action="" method="post">
		<label>KeyMap:</label>
		<select name="keymap" onchange="document.getElementById('content').submit()">
			<option value="normal" <?php if($keymap=='normal') echo 'selected="selected"'; ?>>Normal</option>
			<option value="vim" <?php if($keymap=='vim') echo 'selected="selected"'; ?>>VIM</option>
			<option value="emacs" <?php if($keymap=='emacs') echo 'selected="selected"'; ?>>Emacs</option>
		</select>
		<label>Language:</label>
		<select name="language" onchange="document.getElementById('content').submit()">
			<?php foreach($lang_info as $key => $value) { ?>
			<option value="<?php echo $key; ?>" <?php if($key==$language) echo 'selected="selected"'; ?>><?php echo $key; ?></option>
			<?php } ?>
		</select>
		<input type="submit" value="Save" />
		<textarea id="myTextArea" name="code"><?php echo $code; ?></textarea>
	</form>
</div>
<script>

	var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
		lineNumbers: true,
		matchBrackets: true,
		<?php if($keymap == 'vim') {
			echo 'keyMap: "vim",';
		} else if($keymap == 'emacs') {
			echo 'keyMap: "emacs",';
		} ?>
		mode: '<?php echo $lang[0]; ?>',
		indentUnit: 4,
		indentWithTabs: true,
	});

</script>
</body>
</html>
