<?php
  if( isset( $_POST['submit'] ) && isset( $_POST['survey-slug'] ) && !empty( $_POST['survey-slug'] ) ){
    update_option( 'survey-slug', $_POST['survey-slug'] );
  }

  $slug = get_option( 'survey-slug' );
  $slug = $slug ? $slug : 'space_survey';
?>
<form method="post">
  <table class="form-table" role="presentation">
    <tbody>
      <tr>
        <th scope="row">
          <label for="survey-slug">Slug</label></th>
          <td>
            <input type="text" class="regular-text" id="survey-slug" name="survey-slug" value="<?php echo $slug;?>">
          </td>
      </tr>
    </tbody>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p></form>
</form>
