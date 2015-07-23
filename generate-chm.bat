@echo off
set HTMLHELP_PATH=..\_htmlhelp

if exist _Docs rd _Docs /s /q

echo.
echo API
php apidoc api --overwrite --template=bootstrapchm vendor/yiisoft/yii2-dev,vendor/yiisoft/yii2-apidoc,vendor/yiisoft/yii2-authclient,vendor/yiisoft/yii2-bootstrap,vendor/yiisoft/yii2-codeception,vendor/yiisoft/yi
i2-composer,vendor/yiisoft/yii2-debug,vendor/yiisoft/yii2-elasticsearch,vendor/yiisoft/yii2-faker,vendor/yiisoft/yii2-gii,vendor/yiisoft/yii2-imagine,vendor/yiisoft/yii2-jui,vendor/yiisoft/yii2-mongodb,vendor/yii
soft/yii2-redis,vendor/yiisoft/yii2-smarty,vendor/yiisoft/yii2-sphinx,vendor/yiisoft/yii2-swiftmailer,vendor/yiisoft/yii2-twig ./_Docs

echo.
echo Guide
php apidoc guide --overwrite --template=bootstrapchm vendor/yiisoft/yii2-dev/docs/guide ./_Docs

echo.
echo CHM
php apidoc chm --overwrite --template=bootstrapchm ./_Docs vendor/yiisoft/yii2-dev/docs/guide

echo.
echo Generating CHM
%HTMLHELP_PATH%\hhc.exe _Docs\_yii2-chm_project.hhp

pause
