See English readme [below](#system-requirements).

Требования к CMS
----------------

Поддерживаются все версии Magento, начиная с 1.5 и более свежие.

Установка модуля в магазин
--------------------------

1. [Скачайте архив модуля](https://s3.amazonaws.com/convead/public/plugins/magento/Convead-1.0.0.tgz) из нашего репозитория.
2. Установите модуль через Magento Connect Manager или через FTP.

  **Установка через Magento Connect Manager:**
  * В панели администратора перейдите в раздел «System» → «Magento Connect» → «Magento Connect Manager». Введите ваши логин и пароль от панели администратора магазина.  
  * В секции «Direct package file upload» нажмите кнопку «Обзор» и укажите архив с модулем на вашем компьютере. Нажмите кнопку «Upload».
  * Если модуль установился, то в нижней части окна Magento Connect Manager должны появиться надписи «Package installed: community Convead» и «Procedure completed». Если у вас не получается установка модуля этим способом - попробуйте установить его через FTP.

  **Загрузка через FTP:**
  * Распакуйте архив с модулем и загрузите папку «app» из него через FTP в корень вашего сайта. Замените все существующие файлы при необходимости.
  * Очистите кеш Magento: в панели администратора перейдите в раздел «System» → «Cache Managemet» и нажмите кнопку «Flush Magento Cache».

3. Перейдите в панель администратора вашего магазина, раздел «System» → «Configuration».

4. В сайдбаре перейдите в меню «Sales» → «Sales».

5. В разделе «Convead Settings» введите API-ключ вашего аккаунта в поле «API-key», установите переключатель «Enabled» в «Yes» и сохраните изменения кнопкой «Save Config».

System requirements
-------------------

All Magento versions starting with 1.5 and later are supported.

Installing module
-----------------

1. [Download module archive](https://s3.amazonaws.com/convead/public/plugins/magento/Convead-1.0.0.tgz) from our repository.
2. Install module with Magento Connect Manager or via FTP.

  **Installing with Magento Connect Manager:**
  * Log in to administation panel of your store and navigate to "System" → "Magento Connect" → "Magento Connect Manager". Enter your admin login and password.
  * Press "Browse" button in "Direct package file upload" section and upload a module package from your computer.
  * If module has installed successfully you will see "Package installed: community Convead" and "Procedure completed" labels at the bottom of Magento Connect Manager window. If something goes wrong try uploading plugin via FTP.

  **Uploading via FTP:**
  * Unpack downloaded archive and upload folder "app" from its contents via FTP to the root of your website. Replace all existing files if needed.
  * Flush Magento cache: in administation panel navigate to "System" → "Cache Managemet" and press "Flush Magento Cache" button.

3. Log in to administation panel of your store and navigate to "System" → "Configuration".
4. Navigate to «Sales» → «Sales» in sidebar.
5. Fill in "API-key" field with your Convead account's API key within "Convead Settings" section, set dropdown control "Enabled" to "Yes" and click "Save Config".
