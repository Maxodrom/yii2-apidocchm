@echo off
set HTMLHELP_PATH=..\_htmlhelp

echo.
echo API
php apidoc api --overwrite --template=bootstrapchm vendor/yiisoft/yii2-dev ./_Docs

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
