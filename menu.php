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
        <form action="" method="post">
            <table class="form-table">
            	<tr>
					<th scope="row">
						<label for="ques_title">Title</label>
					</th>
					<td>
						<textarea name="ques_title" rows="3" cols="60"></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="ques_description">Description</label>
					</th>
					<td>
						<textarea name="ques_description" rows="3" cols="60"></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="ques_rank">Rank</label>
					</th>
					<td>
						<input type="number" name="ques_rank">
					</td>
				</tr>

				<tr>
					<th scope="row">Type </th>
					<td>
						<fieldset>
							<p>
								<label><input name="ques_type" type="radio" value="single" > Single</label>
								<br>
								<label><input name="ques_type" type="radio" value="multiple"> Multiple</label>
								<br>
								<label><input name="ques_type" type="radio" value="range"> Multiple</label>
							</p>
						</fieldset>
					</td>
				</tr>				

				<tr>
					<th scope="row">
						<label for="ques_parent">Parent</label>
					</th>
					<td>
						<input type="text" name="ques_parent">
					</td>
				</tr>	
            </table>

            <p class="submit">
            	<input type="submit" name="submit" class="button button-primary" value="Save Changes">
            </p>
        </form>
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
