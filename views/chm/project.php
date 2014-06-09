[OPTIONS]
Binary Index=Yes
Compatibility=1.1 or later
Compiled file=_yii2.chm
Contents file=_yii2-chm_toc.hhc
Default Window=main
Default topic=guide-README.html
Full text search stop list file=_yii2-chm_stop.stp
Full-text search=Yes
Index file=_yii2-chm_index.hhk
Language=0x409

[WINDOWS]
main=,"_yii2-chm_toc.hhc","_yii2-chm_index.hhk","guide-README.html",,,,,,0x2520,,0x300e,,,,,,,,0

[FILES]
<?php foreach ($files as $file): ?>
<?= $file ?>

<?php endforeach; ?>
