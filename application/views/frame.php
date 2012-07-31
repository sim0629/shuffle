<!DOCTYPE html>
<html>
<head>
<title>Shuffle!</title>
</head>
<frameset cols="*, 3%, 400, 3%" noresize frameborder="0">
    <frame src="<?php echo site_url('shuffle') ?>" noresize />
    <frame noresize />
    <frameset rows="*, 400, 100, *" noresize scrolling="no">
        <frame noresize />
        <frame name="player" src="" scrolling="no" noresize />
        <frame name="controller" src="<?php echo site_url('shuffle/controller') ?>" scrolling="no" noresize />
        <frame noresize />
    </frameset>
    <frame noresize />
</frameset>
</html>
