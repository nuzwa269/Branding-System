<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    CoachPro_AI_Social_Branding
 * @subpackage CoachPro_AI_Social_Branding/includes
 */
class CPAI_TSB_Activator {

	/**
	 * Runs on activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		if ( false === get_option( 'cpai_tsb_platforms' ) ) {
			update_option( 'cpai_tsb_platforms', self::default_platforms() );
		}

		if ( false === get_option( 'cpai_tsb_settings' ) ) {
			update_option(
				'cpai_tsb_settings',
				array(
					'default_language' => 'en',
					'show_branding'    => 1,
					'items_per_page'   => 10,
					'dashboard_title'  => 'Teacher Social Branding Dashboard',
					'primary_color'    => '#2271b1',
				)
			);
		}
	}

	/**
	 * Get default platform data.
	 *
	 * @return array
	 */
	private static function default_platforms() {
		return array(
			'facebook'  => self::build_platform_defaults( 'facebook', 'Facebook', '#1877F2', 'Analyze Facebook', '#e8f1ff', 10, 'fab fa-facebook-f' ),
			'youtube'   => self::build_platform_defaults( 'youtube', 'YouTube', '#FF0000', 'Analyze YouTube', '#fff1f2', 20, 'fab fa-youtube' ),
			'instagram' => self::build_platform_defaults( 'instagram', 'Instagram', '#C13584', 'Analyze Instagram', '#fff1f7', 30, 'fab fa-instagram' ),
			'tiktok'    => self::build_platform_defaults( 'tiktok', 'TikTok', '#000000', 'Analyze TikTok', '#f3f4f6', 40, 'fab fa-tiktok' ),
		);
	}

	/**
	 * Build defaults for a single platform.
	 *
	 * @param string $id Platform id.
	 * @param string $name Platform name.
	 * @param string $color Platform color.
	 * @param string $button_label Button label.
	 *
	 * @return array
	 */
	private static function build_platform_defaults( $id, $name, $color, $button_label, $light_color = "#eff6ff", $sort_order = 10, $icon = "" ) {
		$questions = 'facebook' === $id ? self::default_facebook_questions() : array( self::empty_question( 1 ) );

		return array(
			'id'           => $id,
			'name_en'      => $name,
			'name_ur'      => '',
			'icon'         => $icon,
			'enabled'      => 1,
			'sort_order'   => absint( $sort_order ),
			'title'        => $name . ' Optimization',
			'description'  => 'Manage your ' . $name . '-specific checklist and guidance.',
			'color'        => $color,
			'light_color'  => $light_color,
			'button_label' => $button_label,
			'questions'    => $questions,
		);
	}

	/**
	 * Facebook teacher personal-branding starter questions.
	 *
	 * @return array
	 */
	private static function default_facebook_questions() {
		return array(
			array(
				'id'      => 'q1',
				'text_en' => 'Is your Facebook page profile image a clear professional headshot with consistent branding colors?',
				'text_ur' => 'کیا آپ کے فیس بک پیج کی پروفائل تصویر واضح پروفیشنل ہیڈ شاٹ ہے اور برانڈنگ کلرز سے میچ کرتی ہے؟',
				'instruction_en' => array(
					'title' => 'Upgrade your profile image',
					'steps' => array( 'Use a high-resolution portrait with good lighting.', 'Wear classroom-appropriate attire to reflect your teaching identity.', 'Apply one consistent color tone that matches your page visuals.' ),
					'tips'  => array( 'Avoid logos-only photos. People trust educator brands that show a real face.' ),
					'tool'  => '<em>Tool idea:</em> Canva profile photo frame + basic color filter.',
				),
				'instruction_ur' => array(
					'title' => 'پروفائل تصویر کو بہتر بنائیں',
					'steps' => array( 'ہائی ریزولوشن پورٹریٹ تصویر استعمال کریں۔', 'لباس ایسا رکھیں جو آپ کی تدریسی شناخت کو ظاہر کرے۔', 'ایک مستقل کلر ٹون رکھیں جو پورے پیج سے میچ کرے۔' ),
					'tips'  => array( 'صرف لوگو والی تصویر سے گریز کریں؛ چہرہ اعتماد بڑھاتا ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> Canva پروفائل فریم اور کلر فلٹر۔',
				),
			),
			array(
				'id'      => 'q2',
				'text_en' => 'Does your cover photo clearly communicate your teaching niche and student outcome?',
				'text_ur' => 'کیا کور فوٹو سے آپ کی ٹیچنگ نِچ اور طلبہ کے نتائج واضح طور پر سمجھ آتے ہیں؟',
				'instruction_en' => array(
					'title' => 'Design a message-driven cover',
					'steps' => array( 'Add one short value statement (who you teach + what result).', 'Include your core subject and class level.', 'Keep text readable on mobile preview.' ),
					'tips'  => array( 'A clear promise in the cover area improves first impressions in seconds.' ),
					'tool'  => '<em>Tool idea:</em> Meta cover safe-area template.',
				),
				'instruction_ur' => array(
					'title' => 'پیغام پر مبنی کور تیار کریں',
					'steps' => array( 'ایک مختصر ویلیو اسٹیٹمنٹ لکھیں (کسے پڑھاتے ہیں اور کیا نتیجہ دیتے ہیں)۔', 'اپنا مضمون اور کلاس لیول نمایاں کریں۔', 'ٹیکسٹ موبائل پر بھی واضح اور پڑھنے کے قابل رکھیں۔' ),
					'tips'  => array( 'کور پر واضح وعدہ چند سیکنڈ میں مضبوط پہلا تاثر دیتا ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> فیس بک کور سیف ایریا ٹیمپلیٹ۔',
				),
			),
			array(
				'id'      => 'q3',
				'text_en' => 'Is your intro/bio written in student-focused language instead of generic teacher statements?',
				'text_ur' => 'کیا آپ کا انٹرو/بایو عام جملوں کے بجائے طالب علم کی ضرورت کے مطابق لکھا گیا ہے؟',
				'instruction_en' => array(
					'title' => 'Rewrite your intro for impact',
					'steps' => array( 'Start with the audience you serve.', 'Mention one measurable learning result.', 'End with a friendly call to action such as message for guidance.' ),
					'tips'  => array( 'Use simple language parents and students can understand immediately.' ),
					'tool'  => '<em>Tool idea:</em> 3-line bio formula worksheet.',
				),
				'instruction_ur' => array(
					'title' => 'انٹرو کو اثر انگیز بنائیں',
					'steps' => array( 'پہلی لائن میں واضح کریں کہ آپ کن طلبہ کی مدد کرتے ہیں۔', 'ایک قابلِ پیمائش نتیجہ شامل کریں۔', 'آخر میں میسج/رابطے کی آسان کال ٹو ایکشن دیں۔' ),
					'tips'  => array( 'سادہ زبان استعمال کریں تاکہ والدین اور طلبہ فوراً سمجھ سکیں۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> 3 لائن بایو فارمولا۔',
				),
			),
			array(
				'id'      => 'q4',
				'text_en' => 'Do you have a fixed weekly content plan (tips, live class snippets, success stories, FAQs)?',
				'text_ur' => 'کیا آپ کے پاس ہفتہ وار فکسڈ کنٹینٹ پلان موجود ہے (ٹپس، لائیو کلپس، کامیابی کہانیاں، FAQ)؟',
				'instruction_en' => array(
					'title' => 'Build a weekly content rhythm',
					'steps' => array( 'Create 4 content pillars and assign one day to each.', 'Batch-create post ideas every weekend.', 'Track engagement and repeat top-performing formats.' ),
					'tips'  => array( 'Consistency beats perfection for personal brand growth.' ),
					'tool'  => '<em>Tool idea:</em> Google Sheet content calendar.',
				),
				'instruction_ur' => array(
					'title' => 'ہفتہ وار کنٹینٹ روٹین بنائیں',
					'steps' => array( 'چار کنٹینٹ ستون بنائیں اور ہر ایک کے لیے دن مقرر کریں۔', 'ویک اینڈ پر اگلے ہفتے کے پوسٹ آئیڈیاز تیار کریں۔', 'اینگیجمنٹ دیکھیں اور بہترین فارمیٹ دوبارہ استعمال کریں۔' ),
					'tips'  => array( 'پرسنل برانڈنگ میں مستقل مزاجی، کمال سے زیادہ اہم ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> Google Sheet کنٹینٹ کیلنڈر۔',
				),
			),
			array(
				'id'      => 'q5',
				'text_en' => 'Are your posts using simple hooks in the first 2 lines to stop scrolling?',
				'text_ur' => 'کیا آپ کی پوسٹس کی پہلی دو لائنیں مضبوط ہُک رکھتی ہیں تاکہ لوگ اسکرول روکیں؟',
				'instruction_en' => array(
					'title' => 'Improve your post openings',
					'steps' => array( 'Open with a student problem question.', 'Add a bold promise or number-based result.', 'Keep first line under 8 words where possible.' ),
					'tips'  => array( 'Strong hooks increase reach even without paid ads.' ),
					'tool'  => '<em>Tool idea:</em> Hook swipe file for educators.',
				),
				'instruction_ur' => array(
					'title' => 'پوسٹ کی شروعات طاقتور بنائیں',
					'steps' => array( 'پہلی لائن میں طلبہ کا مسئلہ سوال کی شکل میں لکھیں۔', 'نتیجہ یا عددی وعدہ شامل کریں۔', 'ممکن ہو تو پہلی لائن آٹھ الفاظ سے کم رکھیں۔' ),
					'tips'  => array( 'اچھی ہُک بغیر اشتہار کے بھی رِیچ بڑھا دیتی ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> ایجوکیٹر ہُک سوائپ فائل۔',
				),
			),
			array(
				'id'      => 'q6',
				'text_en' => 'Do you showcase social proof like student testimonials, grades, or parent feedback regularly?',
				'text_ur' => 'کیا آپ باقاعدگی سے سوشل پروف دکھاتے ہیں جیسے ٹیسٹی مونیلز، گریڈز یا والدین کا فیڈبیک؟',
				'instruction_en' => array(
					'title' => 'Strengthen trust with proof',
					'steps' => array( 'Post one proof-based story each week.', 'Blur sensitive student data before sharing.', 'Add context: what strategy produced the result.' ),
					'tips'  => array( 'Specific proof converts better than generic praise.' ),
					'tool'  => '<em>Tool idea:</em> Testimonial highlight template.',
				),
				'instruction_ur' => array(
					'title' => 'ثبوت کے ذریعے اعتماد بڑھائیں',
					'steps' => array( 'ہر ہفتے کم از کم ایک ثبوت پر مبنی پوسٹ کریں۔', 'طلبہ کا حساس ڈیٹا شیئر کرنے سے پہلے بلر کریں۔', 'یہ بھی بتائیں کہ نتیجہ کس حکمتِ عملی سے ملا۔' ),
					'tips'  => array( 'مخصوص ثبوت عام تعریف سے کہیں زیادہ اثر کرتا ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> Testimonial ہائی لائٹ ٹیمپلیٹ۔',
				),
			),
			array(
				'id'      => 'q7',
				'text_en' => 'Is your page CTA button aligned with your goal (Message, WhatsApp, Book Now, Learn More)?',
				'text_ur' => 'کیا پیج کا CTA بٹن آپ کے اصل مقصد کے مطابق ہے (Message، WhatsApp، Book Now، Learn More)؟',
				'instruction_en' => array(
					'title' => 'Align call-to-action with conversion',
					'steps' => array( 'Set one primary action for new visitors.', 'Match button text with the offer in your pinned post.', 'Test CTA response weekly and refine.' ),
					'tips'  => array( 'One clear CTA avoids confusion and improves inquiries.' ),
					'tool'  => '<em>Tool idea:</em> CTA audit checklist.',
				),
				'instruction_ur' => array(
					'title' => 'CTA کو مقصد کے ساتھ ہم آہنگ کریں',
					'steps' => array( 'نئے وزیٹر کے لیے ایک بنیادی ایکشن منتخب کریں۔', 'بٹن ٹیکسٹ کو پن پوسٹ کی آفر سے میچ کریں۔', 'ہر ہفتے CTA ریسپانس چیک کر کے بہتری لائیں۔' ),
					'tips'  => array( 'واضح CTA کنفیوژن کم اور انکوائریز زیادہ کرتا ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> CTA آڈٹ چیک لسٹ۔',
				),
			),
			array(
				'id'      => 'q8',
				'text_en' => 'Have you pinned a high-converting post that introduces your teaching offer and contact process?',
				'text_ur' => 'کیا آپ نے ایک ہائی کنورٹنگ پن پوسٹ لگائی ہے جس میں آپ کی آفر اور رابطہ طریقہ واضح ہو؟',
				'instruction_en' => array(
					'title' => 'Create a strategic pinned post',
					'steps' => array( 'Explain who the program is for.', 'Add key benefits in bullet points.', 'Include exact next step: message keyword or booking link.' ),
					'tips'  => array( 'Pinned posts act as your landing page on Facebook.' ),
					'tool'  => '<em>Tool idea:</em> Pinned post conversion copy template.',
				),
				'instruction_ur' => array(
					'title' => 'اسٹریٹیجک پن پوسٹ تیار کریں',
					'steps' => array( 'واضح کریں کہ پروگرام کن طلبہ کے لیے ہے۔', 'اہم فوائد پوائنٹس میں لکھیں۔', 'اگلا قدم واضح دیں: میسج کی ورڈ یا بکنگ لنک۔' ),
					'tips'  => array( 'فیس بک پر پن پوسٹ آپ کے لینڈنگ پیج کا کام دیتی ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> پن پوسٹ کنورژن کاپی ٹیمپلیٹ۔',
				),
			),
			array(
				'id'      => 'q9',
				'text_en' => 'Do you respond to comments and inbox messages within 24 hours with a professional script?',
				'text_ur' => 'کیا آپ 24 گھنٹوں کے اندر کمنٹس اور ان باکس میسجز کا پروفیشنل اسکرپٹ کے ساتھ جواب دیتے ہیں؟',
				'instruction_en' => array(
					'title' => 'Improve response system',
					'steps' => array( 'Prepare 3 quick-reply scripts for common questions.', 'Use polite greetings and name personalization.', 'End replies with one clear next action.' ),
					'tips'  => array( 'Fast response time increases trust and enrollment chances.' ),
					'tool'  => '<em>Tool idea:</em> Saved replies in Meta Business Suite.',
				),
				'instruction_ur' => array(
					'title' => 'رسپانس سسٹم بہتر کریں',
					'steps' => array( 'عام سوالات کے لیے 3 فوری جواب اسکرپٹس بنائیں۔', 'ادب سے سلام اور نام کے ساتھ پرسنلائز جواب دیں۔', 'ہر جواب کا اختتام واضح اگلے قدم پر کریں۔' ),
					'tips'  => array( 'تیز جواب سے اعتماد اور انرولمنٹ کے امکانات بڑھتے ہیں۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> Meta Business Suite saved replies۔',
				),
			),
			array(
				'id'      => 'q10',
				'text_en' => 'Are you reviewing Facebook Insights monthly to improve content, timing, and audience targeting?',
				'text_ur' => 'کیا آپ ماہانہ Facebook Insights دیکھ کر کنٹینٹ، ٹائمنگ اور آڈینس ٹارگٹنگ بہتر کرتے ہیں؟',
				'instruction_en' => array(
					'title' => 'Use insights for continuous growth',
					'steps' => array( 'Track top 5 posts by reach and saves.', 'Identify best posting time from engagement trends.', 'Drop low-performing formats and double down on winners.' ),
					'tips'  => array( 'Data-led improvement is the fastest path to a stronger educator brand.' ),
					'tool'  => '<em>Tool idea:</em> Monthly insights review sheet.',
				),
				'instruction_ur' => array(
					'title' => 'Insights سے مسلسل گروتھ حاصل کریں',
					'steps' => array( 'رِیچ اور سیوز کے مطابق ٹاپ 5 پوسٹس نوٹ کریں۔', 'اینگیجمنٹ ٹرینڈ سے بہترین پوسٹنگ ٹائم معلوم کریں۔', 'کمزور فارمیٹس ہٹا کر کامیاب فارمیٹس پر فوکس کریں۔' ),
					'tips'  => array( 'ڈیٹا پر مبنی بہتری مضبوط ٹیچر برانڈ کی تیز ترین راہ ہے۔' ),
					'tool'  => '<em>ٹول آئیڈیا:</em> ماہانہ Insights ریویو شیٹ۔',
				),
			),
		);
	}

	/**
	 * Empty question scaffold.
	 *
	 * @param int $index Question position.
	 *
	 * @return array
	 */
	private static function empty_question( $index ) {
		return array(
			'id'             => 'q' . absint( $index ),
			'text_en'        => '',
			'text_ur'        => '',
			'image_url'      => '',
			'compare_left_image_url'  => '',
			'compare_right_image_url' => '',
			'instruction_en' => array(
				'title' => '',
				'steps' => array(),
				'tips'  => array(),
				'tool'  => '',
			),
			'instruction_ur' => array(
				'title' => '',
				'steps' => array(),
				'tips'  => array(),
				'tool'  => '',
			),
		);
	}
}
