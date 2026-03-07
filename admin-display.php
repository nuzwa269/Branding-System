<div class="wrap">
    <h1>COACHPRO AI – Teacher Social Branding System</h1>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="cpai_tsb_save_data">
        <?php wp_nonce_field('cpai_tsb_save_data', 'cpai_tsb_nonce'); ?>

        <?php
        $platforms = get_option('cpai_tsb_platforms', array());
        if (empty($platforms)) {
            echo '<p>No platforms found. Please reactivate the plugin to load defaults.</p>';
        } else {
            foreach ($platforms as $p_index => $platform) {
                ?>
                <div class="cpai-platform-card" style="background: #fff; padding: 20px; margin-bottom: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                    <h2 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        Platform: <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][name_en]" value="<?php echo esc_attr($platform['name_en']); ?>" style="font-size: 1.2em; font-weight: bold;">
                        (Urdu: <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][name_ur]" value="<?php echo esc_attr($platform['name_ur']); ?>">)
                    </h2>

                    <div class="cpai-platform-details" style="margin-bottom: 15px;">
                        <label>Icon Class (FontAwesome): <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][icon]" value="<?php echo esc_attr($platform['icon']); ?>"></label>
                        <label>Color: <input type="color" name="cpai_tsb_platforms[<?php echo $p_index; ?>][color]" value="<?php echo esc_attr($platform['color']); ?>"></label>
                        <input type="hidden" name="cpai_tsb_platforms[<?php echo $p_index; ?>][id]" value="<?php echo esc_attr($platform['id']); ?>">
                    </div>

                    <h3>Questions (10 Required)</h3>
                    <div class="cpai-questions-container">
                        <?php
                        $questions = isset($platform['questions']) ? $platform['questions'] : array();
                        // Ensure 10 slots
                        for ($q = 0; $q < 10; $q++) {
                            $question = isset($questions[$q]) ? $questions[$q] : array(
                                'id' => 'q' . ($q + 1),
                                'text_en' => '', 'text_ur' => '',
                                'instruction_en' => array('title' => '', 'steps' => array(), 'tips' => array()),
                                'instruction_ur' => array('title' => '', 'steps' => array(), 'tips' => array())
                            );
                            ?>
                            <div class="cpai-question-item" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd;">
                                <h4>Question <?php echo $q + 1; ?></h4>
                                <p>
                                    <strong>English:</strong> <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][text_en]" value="<?php echo esc_attr($question['text_en']); ?>" class="widefat"><br>
                                    <strong>Urdu:</strong> <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][text_ur]" value="<?php echo esc_attr($question['text_ur']); ?>" class="widefat" dir="rtl">
                                </p>

                                <div class="cpai-instruction-panel">
                                    <h5>Instruction Panel (When answer is Negative)</h5>

                                    <div style="display: flex; gap: 20px;">
                                        <div style="flex: 1;">
                                            <h6>English Instruction</h6>
                                            <label>Title:</label>
                                            <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_en][title]" value="<?php echo esc_attr($question['instruction_en']['title']); ?>" class="widefat">

                                            <label>Steps (One per line):</label>
                                            <textarea name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_en][steps]" class="widefat" rows="3"><?php echo esc_textarea(implode("\n", isset($question['instruction_en']['steps']) ? $question['instruction_en']['steps'] : array())); ?></textarea>

                                            <label>Tips (One per line):</label>
                                            <textarea name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_en][tips]" class="widefat" rows="3"><?php echo esc_textarea(implode("\n", isset($question['instruction_en']['tips']) ? $question['instruction_en']['tips'] : array())); ?></textarea>
                                        </div>

                                        <div style="flex: 1;">
                                            <h6>Urdu Instruction</h6>
                                            <label>Title:</label>
                                            <input type="text" name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_ur][title]" value="<?php echo esc_attr($question['instruction_ur']['title']); ?>" class="widefat" dir="rtl">

                                            <label>Steps (One per line):</label>
                                            <textarea name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_ur][steps]" class="widefat" rows="3" dir="rtl"><?php echo esc_textarea(implode("\n", isset($question['instruction_ur']['steps']) ? $question['instruction_ur']['steps'] : array())); ?></textarea>

                                            <label>Tips (One per line):</label>
                                            <textarea name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_ur][tips]" class="widefat" rows="3" dir="rtl"><?php echo esc_textarea(implode("\n", isset($question['instruction_ur']['tips']) ? $question['instruction_ur']['tips'] : array())); ?></textarea>
                                        </div>
                                    </div>

                                    <label>Tool Placeholder (HTML/Embed):</label>
                                    <textarea name="cpai_tsb_platforms[<?php echo $p_index; ?>][questions][<?php echo $q; ?>][instruction_en][tool]" class="widefat" rows="2"><?php echo esc_textarea(isset($question['instruction_en']['tool']) ? $question['instruction_en']['tool'] : ''); ?></textarea>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>
</div>
