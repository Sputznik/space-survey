<?php

$guest_db = SPACE_DB_GUEST::getInstance();
$survey_db = SPACE_DB_SURVEY::getInstance();

$guest_id = $_GET['ID'];

if( isset( $_GET['ID'] ) && $_GET['ID'] && isset( $_GET['action'] ) && 'trash' == $_GET['action'] ){
  $guest_db->delete_row( $_GET['ID'] );
  _e( "<script>location.href='?page=space-responses';</script>" );
  wp_die();
}

$guest = $guest_db->get_row( $guest_id );

$survey = $survey_db->get_row( $guest->survey_id );
$questions = wp_unslash( $survey_db->getQuestionsList( $guest->survey_id ) );
$choices = wp_unslash( $survey_db->getChoicesList( $guest->survey_id ) );
$guestResponses = $guest_db->getResponses( $guest_id );

$responses = SPACE_EXPORT::getInstance()->getFormattedResponses( $guestResponses, $questions, $choices );

//ORBIT_UTIL::getInstance()->test( $responses );

$phpdate = strtotime( $guest->created_on );

?>
<div class="wrap">
  <h1 class="wp-heading-inline">View Response</h1>
  <p><a href="<?php _e( admin_url( 'admin.php?page=space-responses' ) );?>">Go Back</a></p>
  <div id="profile-card">
    <div class="profile-avatar"><span class="dashicons dashicons-id-alt"></span></div>
    <div class="profile-info">
      <h3><?php _e( $guest->ipaddress );?> (IP ADDRESS)</h3>
      <p><b><a target="_blank" href="<?php echo get_the_permalink( $survey->ID );?>"><?php _e( $survey->post_title ); ?></b></a> on <b><?php _e( date("F d, Y h:i:s", $phpdate ) );?></b></p>
      <p class='profile-meta'><?php _e( $guest->meta );?></p>
    </div>
  </div>

  <table id="guest-table">
    <tr>
      <th>Question</th>
      <th>Choices</th>
    </tr>
    <?php foreach( $responses as $response ): ?>
    <tr>
      <td><?php _e( $response['question_title'] );?></td>
      <td><?php _e( implode( ', ', $response['choices'] ) );?></td>
    </tr>
    <?php endforeach;?>
  </table>
</div>
<style>
  #profile-card{
    border: #eee solid 1px;
    display: grid;
    grid-template-columns: 100px 1fr;
    max-width: 700px;
    background: #fff;
    margin-top: 30px;
    padding: 15px;
    grid-gap: 20px;
    box-sizing: border-box;
  }
  #profile-card .profile-meta{
    color: #999;
  }
  #profile-card .profile-avatar span{ font-size: 90px; }

  #guest-table{

    margin-top: 30px;
    border-collapse: collapse;
    width: 100%;
    max-width: 700px;
  }
  #guest-table td, #guest-table th {
    border: 1px solid #ddd;
    padding: 8px;
    min-width: 150px;
  }
  #guest-table tr:nth-child(even){background-color: #FFF;}
  #guest-table th{background-color: #4CAF50; color: white;}
</style>
