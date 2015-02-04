Dropbox
===========

BoxBilling extension for file uploading to DropBox

##Installing extension
1. Download this repository by using `git clone` or `Download ZIP` button.
2. Create folder `Dropbox` in `bb-modules` directory.
3. Move all the files you downloaded to this folder.
4. Go to `Extensions->Overview` in BoxBilling admin area find `Dropbox` extension and click `Activate`. You will be redirected to settings view.
5. Follow the istructions in next screen and generate Dropbox access code.
6. Paste it into field in extension settings page and click Update. Extension is ready to use!

##Using extension
This example will show you how to add ticket attachment functionality but feel free to use it in another context;

1. Please copy template you want to edit e.g. `bb-modules/Support/html_client/mod_support_ticket.phtml` to your theme html folder e.g. `bb-themes/huraga/html` (otherwise during next update it will be overwritten).

2. Add following lines of code to client template you copied where you want upload form and download button to appear. *If file is not uploaded, upload form will be shown, otherwise download button will be visible*

  ``` 
     {% if client.dropbox_has_upload({'rel_id':ticket.id, 'extension' : 'ticket'}) %}
          {% include 'mod_dropbox_download.phtml' with {'rel_id':ticket.id, 'extension' : 'ticket'} %}
      {% else %}
          {% include 'mod_dropbox_upload.phtml' with {'rel_id':ticket.id, 'extension' : 'ticket'} %}
      {% endif %}
  ```

3. Make sure object (in this case ticket object) is present in template and pass object ID as 'rel_id' and change `extension` parameter if you want to use extension for other purposes!
4. Repeat these steps for admin template e.g. `mod_support_ticket.phtml` in order to see download button in admin area.

  ```
      {% if admin.dropbox_has_upload({'rel_id':ticket.id, 'extension' : 'ticket'}) %}
          {% include 'mod_dropbox_download.phtml' with {'rel_id':ticket.id, 'extension' : 'ticket'} %}
      {% endif %}
  ```
