<?php 
// Check if there are any errors and if the errors array is not empty
if (isset($errors) && count($errors) > 0) : ?>
  <div class="error">
    <?php 
    // Loop through each error in the errors array and display it
    foreach ($errors as $error) : ?>
      <p><?php echo $error ?></p>
    <?php endforeach ?>
  </div>
<?php endif ?>