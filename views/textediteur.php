<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>A Simple Page with CKEditor</title>
        <!-- Make sure the path to CKEditor is correct. -->
        <script src="../ckeditor/ckeditor.js"></script>
    </head>
    <body>
        <form action="../controlleurs/Cplan_type.php" method="post">
            <textarea name="editor1" id="editor1" rows="10" cols="80">
                This is my textarea to be replaced with CKEditor.
            </textarea>
            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>

              <input  type="submit" class="form-control btn btn-success" value="Enregistrer" id="submit"  name="savetext" >  
        </form>
    </body>
</html>