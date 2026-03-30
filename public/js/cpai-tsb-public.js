(function( $ ) {
	'use strict';

	class CoachProBrandingSystem {
		constructor(data, rootElement) {
			this.data = data;
			this.root = rootElement;
			this.state = {
				lang: 'en',
				activePlatformIndex: 0,
				answers: {}
			};

			this.elements = {
				wrapper: this.root,
				nav: this.root.find('.cpai-tsb-platforms-nav').first(),
				content: this.root.find('.cpai-tsb-content').first(),
				langBtns: this.root.find('.cpai-lang-btn')
			};

			this.init();
		}

		init() {
			this.renderTitle();
			this.renderNav();
			this.renderContent();
			this.updateLanguage();
			this.attachEvents();
		}


		renderTitle() {
			const title = (this.data.strings && this.data.strings.title) ? this.data.strings.title : '';
			if (!title) {
				return;
			}
			const titleEl = this.elements.wrapper.find('.cpai-tsb-main-title');
			titleEl.html(String(title).replace(/\n/g, '<br>'));
		}

		renderNav() {
			this.elements.nav.empty();
			this.data.platforms.forEach((platform, index) => {
				const isActive = index === this.state.activePlatformIndex ? 'active' : '';
				const icon = platform.icon ? `<i class="${platform.icon}"></i>` : '';
				const btn = $(
					`<button class="cpai-platform-tab ${isActive}" data-index="${index}">${icon}<span class="platform-label"></span></button>`
				);
				btn.find('.platform-label').text(platform.name_en || platform.id || `Platform ${index + 1}`);
				this.elements.nav.append(btn);
			});
		}

		renderContent() {
			this.elements.content.empty();

			this.data.platforms.forEach((platform, pIndex) => {
				const isActive = pIndex === this.state.activePlatformIndex ? 'active' : '';
				const theme = this.getPlatformTheme(platform);
				const platformContainer = $(`<section class="cpai-platform-container ${isActive}" data-index="${pIndex}"></section>`);
				platformContainer.css({
					'--cpai-platform-accent': theme.accent,
					'--cpai-platform-soft': theme.soft,
					'--cpai-platform-gradient': theme.gradient
				});

				const totalQuestions = Array.isArray(platform.questions) ? platform.questions.length : 0;
				const progressShell = $(
					`<div class="cpai-progress-shell">
						<div class="cpai-progress-head">
							<h3 class="cpai-progress-title" data-en="Optimization Progress" data-ur="ترقی کی پیشرفت">Optimization Progress</h3>
							<div class="cpai-progress-text">0 / ${totalQuestions} ${this.data.strings.completed}</div>
						</div>
						<div class="cpai-progress-track"><div class="cpai-progress-bar" style="width:0%"></div></div>
					</div>`
				);
				platformContainer.append(progressShell);

				if (totalQuestions > 0) {
					platform.questions.forEach((q, qIndex) => {
						platformContainer.append(this.createQuestionCard(q, platform.id, qIndex));
					});
				} else {
					platformContainer.append('<div class="cpai-empty-state">Questions will appear here once platform entries are added.</div>');
				}

				const nextBtn = $(`<button class="cpai-next-phase-btn" style="display:none;">${this.data.strings.next_phase}</button>`);
				nextBtn.on('click', () => this.switchPlatform(pIndex + 1));
				platformContainer.append(nextBtn);
				this.elements.content.append(platformContainer);
			});
		}

		createQuestionCard(question, platformId, index) {
			const questionId = question.id || `q${index + 1}`;
			const model = this.normalizeQuestionModel(question, index);

			const card = $(`<article class="cpai-question-card" id="q-card-${platformId}-${questionId}" data-id="${questionId}"></article>`);
			card.data('model', model);

			const compareImages = this.createCompareImages(model.compare);
			if (compareImages) {
				card.append(compareImages);
			}

			const title = $('<h4 class="cpai-question-text"></h4>')
				.attr('data-en', model.question.en)
				.attr('data-ur', model.question.ur)
				.text(model.question.en);


			const comparison = $('<div class="cpai-compare-grid"></div>');
			comparison.append(this.createComparePanel(model.compare.left, 'left'));
			comparison.append(this.createComparePanel(model.compare.right, 'right'));

			const options = $(
				`<div class="cpai-options">
					<button class="cpai-btn btn-no" data-val="no"></button>
					<button class="cpai-btn btn-yes" data-val="yes"></button>
				</div>`
			);

			const optimizationPanel = $(
				`<div class="cpai-optimization-panel">
					<div class="cpai-optimization-title"></div>
					<ul class="cpai-suggestion-list"></ul>
					<div class="cpai-tips-card">
						<div class="cpai-tips-title" data-en="Professional Tips" data-ur="پروفیشنل ٹپس">Professional Tips</div>
						<p class="cpai-tips-copy"></p>
					</div>
					<div class="cpai-tool-card">
						<div class="cpai-tool-title" data-en="Tool Link" data-ur="ٹول لنک">Tool Link</div>
						<div class="cpai-tool-slot"></div>
					</div>
					<div class="cpai-prompt-card">
						<div class="cpai-prompt-title" data-en="Prompt Template" data-ur="پرومٹ ٹیمپلیٹ">Prompt Template</div>
						<pre class="cpai-prompt-slot"></pre>
					</div>
				</div>`
			);

			const successMsg = $('<div class="cpai-success-msg" data-en="Great! Moving to the next check." data-ur="بہت خوب! اگلے چیک کی طرف بڑھتے ہیں۔"></div>');

			card.append(title, comparison, options, optimizationPanel, successMsg);
			this.renderOptimizationPanel(card);
			return card;
		}

		createCompareImages(compare) {
			const leftUrl = compare.left.imageUrl;
			const rightUrl = compare.right.imageUrl;
			if (!leftUrl && !rightUrl) {
				return null;
			}
			const wrap = $('<div class="cpai-compare-images"></div>');
			if (leftUrl) {
				const leftWrap = $('<div class="cpai-compare-img-wrap cpai-needs-improvement"></div>');
				const leftImg = $('<img loading="lazy" />').attr('src', leftUrl).attr('alt', compare.left.title.en || 'Needs Improvement');
				const leftBadge = $('<span class="cpai-img-badge cpai-badge-bad" aria-label="Needs Improvement">🔒</span>');
				leftWrap.append(leftImg, leftBadge);
				wrap.append(leftWrap);
			}
			if (rightUrl) {
				const rightWrap = $('<div class="cpai-compare-img-wrap cpai-recommended"></div>');
				const rightImg = $('<img loading="lazy" />').attr('src', rightUrl).attr('alt', compare.right.title.en || 'Recommended');
				const rightBadge = $('<span class="cpai-img-badge cpai-badge-good" aria-label="Recommended">🔒</span>');
				rightWrap.append(rightImg, rightBadge);
				wrap.append(rightWrap);
			}
			return wrap;
		}

		createComparePanel(panel, side) {
			const panelEl = $(`<div class="cpai-compare-panel ${side}"></div>`);
			const media = $('<div class="cpai-compare-visual" aria-hidden="true"></div>');
			if (panel.imageUrl) {
				const panelImage = $('<img class="cpai-compare-image" loading="lazy" />');
				panelImage.attr('src', panel.imageUrl).attr('alt', panel.title.en || 'Comparison image');
				media.append(panelImage);
			} else {
				media.append(`<i class="${panel.icon}"></i>`);
			}
			const title = $('<div class="cpai-compare-title"></div>').attr('data-en', panel.title.en).attr('data-ur', panel.title.ur).text(panel.title.en);
			const label = $('<div class="cpai-compare-label"></div>').attr('data-en', panel.label.en).attr('data-ur', panel.label.ur).text(panel.label.en);
			panelEl.append(media, title, label);
			return panelEl;
		}


		normalizeQuestionModel(question, index) {
			const defaultNumber = index + 1;
			return {
				question: {
					en: question.text_en || `Optimization question placeholder ${defaultNumber}`,
					ur: question.text_ur || `اصلاحی سوال کا پلیس ہولڈر ${defaultNumber}`
				},
				compare: {
					left: {
						title: {
							en: 'Needs Improvement',
							ur: 'مزید بہتری درکار'
						},
						label: {
							en: 'Less ideal setup',
							ur: 'کم موزوں مثال'
						},
						icon: 'fas fa-layer-group',
							imageUrl: question.compare_left_image_url || ''
					},
					right: {
						title: {
							en: 'Recommended',
							ur: 'تجویز کردہ'
						},
						label: {
							en: 'Professional result',
							ur: 'پیشہ ورانہ نتیجہ'
						},
						icon: 'fas fa-award',
							imageUrl: question.compare_right_image_url || ''
					}
				},
				optimization: {
					en: {
						title: question.instruction_en && question.instruction_en.title ? question.instruction_en.title : 'Optimization checklist',
						suggestions: question.instruction_en && Array.isArray(question.instruction_en.steps) && question.instruction_en.steps.length ? question.instruction_en.steps : [
							'Use a clear branded composition and balanced spacing.',
							'Keep element hierarchy simple and visually scannable.',
							'Improve contrast for key UI/profile information.'
						],
						tips: question.instruction_en && Array.isArray(question.instruction_en.tips) && question.instruction_en.tips.length ? question.instruction_en.tips[0] : 'Add practical guidance here for future platform-specific content.',
						tool: question.instruction_en && question.instruction_en.tool ? question.instruction_en.tool : '',
						toolLink: question.instruction_en && question.instruction_en.tool_link ? question.instruction_en.tool_link : (question.instruction_en && question.instruction_en.tool ? question.instruction_en.tool : ''),
						promptTemplate: question.instruction_en && question.instruction_en.prompt_template ? question.instruction_en.prompt_template : ''
					},
					ur: {
						title: question.instruction_ur && question.instruction_ur.title ? question.instruction_ur.title : 'اصلاحی چیک لسٹ',
						suggestions: question.instruction_ur && Array.isArray(question.instruction_ur.steps) && question.instruction_ur.steps.length ? question.instruction_ur.steps : [
							'واضح برانڈڈ ترتیب اور متوازن اسپیسنگ استعمال کریں۔',
							'عناصر کی درجہ بندی سادہ اور قابلِ فہم رکھیں۔',
							'اہم معلومات کے لیے بہتر کنٹراسٹ بنائیں۔'
						],
						tips: question.instruction_ur && Array.isArray(question.instruction_ur.tips) && question.instruction_ur.tips.length ? question.instruction_ur.tips[0] : 'آئندہ پلیٹ فارم مخصوص مواد کے لیے یہاں عملی رہنمائی شامل کریں۔',
						tool: question.instruction_ur && question.instruction_ur.tool ? question.instruction_ur.tool : '',
						toolLink: question.instruction_ur && question.instruction_ur.tool_link ? question.instruction_ur.tool_link : (question.instruction_ur && question.instruction_ur.tool ? question.instruction_ur.tool : ''),
						promptTemplate: question.instruction_ur && question.instruction_ur.prompt_template ? question.instruction_ur.prompt_template : ''
					}
				}
			};
		}

		attachEvents() {
			this.elements.nav.on('click', '.cpai-platform-tab', (e) => {
				this.switchPlatform($(e.currentTarget).data('index'));
			});

			this.elements.langBtns.on('click', (e) => {
				this.setLanguage($(e.currentTarget).data('lang'));
			});

			this.elements.content.on('click', '.cpai-btn', (e) => {
				const btn = $(e.currentTarget);
				const card = btn.closest('.cpai-question-card');
				const platformContainer = card.closest('.cpai-platform-container');
				const platformIndex = platformContainer.data('index');
				const platformId = this.data.platforms[platformIndex].id;
				const questionId = card.data('id');
				const isYes = btn.data('val') === 'yes';

				card.find('.cpai-btn').removeClass('selected-yes selected-no');
				card.removeClass('is-complete');
				if (isYes) {
					btn.addClass('selected-yes');
					card.addClass('is-complete');
					card.find('.cpai-optimization-panel').slideUp(150);
					card.find('.cpai-success-msg').fadeIn(150);
					this.moveToNextQuestion(card);
				} else {
					btn.addClass('selected-no');
					card.find('.cpai-success-msg').hide();
					this.renderOptimizationPanel(card);
					card.find('.cpai-optimization-panel').slideDown(150);
				}

				if (!this.state.answers[platformId]) {
					this.state.answers[platformId] = {};
				}
				this.state.answers[platformId][questionId] = isYes ? 'yes' : 'no';
				this.updateProgress(platformIndex);
			});
		}

		moveToNextQuestion(currentCard) {
			const nextCard = currentCard.nextAll('.cpai-question-card').first();
			if (!nextCard.length) {
				return;
			}
			nextCard.addClass('cpai-focus-pulse');
			setTimeout(() => nextCard.removeClass('cpai-focus-pulse'), 1200);
			$('html, body').animate({ scrollTop: nextCard.offset().top - 120 }, 280);
		}

		switchPlatform(index) {
			if (index >= this.data.platforms.length || index < 0) {
				return;
			}

			this.state.activePlatformIndex = index;
			this.elements.nav.find('.cpai-platform-tab').removeClass('active');
			this.elements.nav.find(`.cpai-platform-tab[data-index="${index}"]`).addClass('active');

			this.elements.content.find('.cpai-platform-container').removeClass('active').hide();
			const activeContainer = this.elements.content.find(`.cpai-platform-container[data-index="${index}"]`);
			activeContainer.addClass('active').fadeIn();

			$('html, body').animate({ scrollTop: this.elements.wrapper.offset().top - 100 }, 300);
		}

		setLanguage(lang) {
			this.state.lang = lang;
			this.elements.langBtns.removeClass('active');
			this.elements.langBtns.filter(`[data-lang="${lang}"]`).addClass('active');
			this.updateLanguage();
		}

		updateLanguage() {
			const lang = this.state.lang;
			const isUrdu = lang === 'ur';

			this.elements.wrapper.attr('dir', isUrdu ? 'rtl' : 'ltr');

			this.elements.nav.find('.cpai-platform-tab').each((i, el) => {
				const label = isUrdu ? this.data.platforms[i].name_ur : this.data.platforms[i].name_en;
				$(el).find('.platform-label').text(label || this.data.platforms[i].name_en || 'Platform');
			});

			this.elements.content.find('[data-en][data-ur]').each((i, el) => {
				$(el).text($(el).data(lang));
			});

			this.elements.content.find('.cpai-question-card').each((i, el) => {
				this.renderOptimizationPanel($(el));
			});

			$('.cpai-next-phase-btn').text(isUrdu ? 'اگلے مرحلے پر جائیں' : 'Go to Next Phase');
			$('.btn-yes').text(isUrdu ? 'جی ہاں' : 'Yes');
			$('.btn-no').text(isUrdu ? 'نہیں' : 'No');
		}

		renderOptimizationPanel(card) {
			const model = card.data('model');
			if (!model || !model.optimization) {
				return;
			}
			const lang = this.state.lang;
			const content = model.optimization[lang] || model.optimization.en;

			card.find('.cpai-optimization-title').text(content.title);

			const list = card.find('.cpai-suggestion-list');
			list.empty();
			(content.suggestions || []).forEach((item) => {
				list.append(`<li><span class="cpai-suggestion-icon"><i class="fas fa-check-circle"></i></span><span>${item}</span></li>`);
			});

			card.find('.cpai-tips-copy').text(content.tips || '');

			const toolCard = card.find('.cpai-tool-card');
			const toolSlot = card.find('.cpai-tool-slot');
			const toolMarkup = this.formatToolMarkup(content.toolLink);
			if (toolMarkup) {
				toolSlot.html(toolMarkup);
				toolCard.show();
			} else {
				toolSlot.empty();
				toolCard.hide();
			}

			const promptCard = card.find('.cpai-prompt-card');
			const promptSlot = card.find('.cpai-prompt-slot');
			if (content.promptTemplate) {
				promptSlot.text(content.promptTemplate);
				promptCard.show();
			} else {
				promptSlot.text('');
				promptCard.hide();
			}
		}

		formatToolMarkup(toolLink) {
			if (!toolLink) {
				return '';
			}

			const trimmed = String(toolLink).trim();
			if (!trimmed) {
				return '';
			}

			if (/^https?:\/\//i.test(trimmed)) {
				const safeUrl = $('<div>').text(trimmed).html();
				return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">${safeUrl}</a>`;
			}

			return trimmed;
		}

		updateProgress(platformIndex) {
			const platform = this.data.platforms[platformIndex];
			const platformId = platform.id;
			const answers = this.state.answers[platformId] || {};
			const count = Object.keys(answers).length;
			const total = Array.isArray(platform.questions) ? platform.questions.length : 0;

			const container = this.elements.content.find(`.cpai-platform-container[data-index="${platformIndex}"]`);
			const progressBar = container.find('.cpai-progress-bar');
			const progressText = container.find('.cpai-progress-text');
			const nextBtn = container.find('.cpai-next-phase-btn');

			const percentage = total > 0 ? (count / total) * 100 : 0;
			progressBar.css('width', `${percentage}%`);
			progressText.text(`${count} / ${total} ${this.data.strings.completed}`);

			if (total > 0 && count >= total) {
				nextBtn.fadeIn();
			} else {
				nextBtn.hide();
			}
		}

		getPlatformTheme(platform) {
			const accent = platform.color || '#2563eb';
			const soft = platform.light_color || '#eff6ff';
			return {
				accent,
				soft,
				gradient: `linear-gradient(135deg, ${soft} 0%, ${accent} 100%)`
			};
		}
	}

	$(document).ready(function() {
		if (!window.cpai_tsb_data || !Array.isArray(window.cpai_tsb_data.platforms)) {
			return;
		}

		$('.cpai-tsb-wrapper').each(function() {
			new CoachProBrandingSystem(window.cpai_tsb_data, $(this));
		});
	});

})( jQuery );
