<?php 

function space_survey_question_form()
{
    // check user capabilities
    if (! current_user_can('manage_options') ) {
        return;
    }
    ?>
    <div class="wrap yka_survey_question">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        
    </div>
    <?php
}


function space_survey_question_menu()
{
    add_menu_page(
        'Add Question',
        'Space Survey',
        'manage_options',
        'survey_questions',
        'space_survey_question_form',
        'dashicons-awards'        
    );
}
add_action('admin_menu', 'space_survey_question_menu');
