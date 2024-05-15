/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{
	XenForo.MediaGallery =
	{
		SetAllImageTitles: function($input)
		{
			$input.click(function()
			{				
				$titles = $('#SetAllImageTitleText').val();
				$('#SetAllImageTitleText').val('');
				
				if (!$titles)
				{
					return;
				}

				$('.SetImageTitle').each(function(index, value)
				{
					var self = $(value),
						titles = $titles;

					titles = titles.replace('%f', self.data('filename'));
					titles = titles.replace('%n', index + 1);

					$(this).val(titles);
				});
			});		
		},
		
		SetAllImageDescriptions: function($input)
		{
			$input.click(function()
			{				
				this.titles = $('#SetAllImageDescriptionText').val();
				$('#SetAllImageDescriptionText').val('');
				
				if (!this.titles)
				{
					return;
				}
				
				$('.SetImageDescription').val(this.titles);
			});		
		},	
		
		SetAllVideoTitles: function($input)
		{
			$input.click(function()
			{				
				this.titles = $('#SetAllVideoTitleText').val();
				$('#SetAllVideoTitleText').val('');
				
				if (!this.titles)
				{
					return;
				}
				
				$('.SetVideoTitle').val(this.titles);
			});		
		},
		
		SetAllVideoDescriptions: function($input)
		{
			$input.click(function()
			{				
				this.titles = $('#SetAllVideoDescriptionText').val();
				$('#SetAllVideoDescriptionText').val('');
				
				if (!this.titles)
				{
					return;
				}
				
				$('.SetVideoDescription').val(this.titles);
			});		
		},

		InsertFilename: function($element)
		{
			$element.click(function(e)
			{
				e.preventDefault();

				var filename = $element.data('filename'),
					target = $element.data('target');

				$(target).val(filename);
			});
		}
	}

	XenForo.SetAllTitlesForm = function($container)
	{
		var $form = $container.closest('form');

		$form.submit(function(e)
		{
			e.preventDefault();
			return false;
		});
		$container.overlay().close();
	};

	// *********************************************************************

	var insertSpeed = XenForo.speed.normal,
		removeSpeed = XenForo.speed.fast;

	XenForo.AttachmentDownloader = function($element) { this.__construct($element); };
	XenForo.AttachmentDownloader.prototype =
	{
		__construct: function($input)
		{
			this.$input = $input;
			this.url = $input.data('href');
			this.uniqueKey = $input.data('key');

			$('.DownloadTrigger').bind(
			{
				click: $.context(this, 'doDownload')
			});
		},

		doDownload: function(e)
		{
			e.preventDefault();

			this.xhr = XenForo.ajax(
				this.url,
				{
					image_url: this.$input.val()
				},
				$.context(this, 'ajaxSuccess'),
				{
					error: 'failure'
				});

			this.$input.val('');
		},

		ajaxSuccess: function(ajaxData)
		{
			if (ajaxData.error)
			{
				var error = '';

				$.each(ajaxData.error, function(i, errorText) { error += errorText + "\n"; });

				XenForo.alert(error);

				return false;
			}

			$container = $('#AttachmentUploader_image_upload');
			$container.trigger(
			{
				type: 'AttachmentUploaded',
				ajaxData: ajaxData
			});
		}
	};

	XenForo.AttachmentUploader = function($container)
	{
		var ERRORS = {
			TOO_LARGE: 110,
			EMPTY: 120,
			INVALID_EXTENSION: 130
		};

		var $trigger = $($container.data('trigger')),
			$form = $container.closest('form'),
			postParams = {},
			attachmentErrorAlert = function(file, errorCode, message)
			{
				var messageText = $container.data('err-' + errorCode) || message;

				if (!messageText)
				{
					messageText = $container.data('err-unknown');
				}

				if (file)
				{
					XenForo.alert(messageText + '<br /><br />' + XenForo.htmlspecialchars(file.name));
				}
				else
				{
					XenForo.alert(messageText);
				}
			},
			maxFileSize = $container.data('maxfilesize'),
			maxUploads = $container.data('maxuploads'),
			extensions = $container.data('extensions'),
			uniqueKey = $container.data('uniquekey');

		extensions = extensions.replace(/[;*.]/g, '')
		.replace(/,{2,}/g, ',')
		.replace(/^,+/, '').replace(/,+$/, '');

		this.uniqueKey = uniqueKey;

		// --------------------------------------

		// un-hide the upload button
		$container.show();

		var flow,
			useFlow = window.Flow ? true : false;

		var ua = navigator.userAgent;
		if (ua.match(/Android [1-4]/))
		{
			var chrome = ua.match(/Chrome\/([0-9]+)/);
			if (!chrome || parseInt(chrome[1], 10) < 33)
			{
				console.log('Old Android WebView detected. Must fallback to basic uploader.');
				useFlow = false;
			}
		}

		if (useFlow)
		{
			var useFusty = false;

			var flowOptions = {
				target: $container.data('action'),
				allowDuplicateUploads: true,
				fileParameterName: $container.data('postname'),
				query: function()
				{
					var params = $.extend({
						_xfToken: XenForo._csrfToken,
						_xfNoRedirect: 1,
						_xfResponseType: useFusty ? 'json-text' : 'json'
					}, postParams);

					$container.find('.HiddenInput').each(function(i, element)
					{
						var $element = $(element);
						params[$element.data('name')] = $element.data('value');
					});

					return params;
				},
				simultaneousUploads: 1,
				testChunks: false,
				chunkSize: 4 * 1024 * 1024 * 1024 // always one chunk
			};

			flow = new Flow(flowOptions);
			if (!flow.support)
			{
				flow = new FustyFlow(flowOptions);
				useFusty = true;
			}

			var $flowTarget = $('<span />').insertAfter($trigger).append($trigger);

			flow.assignBrowse($flowTarget[0], false, false, {
				accept: '.' + extensions.toLowerCase().replace(/,/g, ',.')
			});

			if (useFusty)
			{
				var outerWidth = $trigger.outerWidth();
				if (outerWidth > 30)
				{
					$flowTarget.css('width', outerWidth);
				}
			}

			$trigger.on('click BeforeOverlayTrigger', function(e)
			{
				e.preventDefault();
			});

			flow.on('fileAdded', function(file)
			{
				var isImage = false;

				// for improved swfupload compat
				file.id = file.uniqueIdentifier;

				switch (file.name.substr(file.name.lastIndexOf('.')).toLowerCase())
				{
					case '.jpg':
					case '.jpeg':
					case '.jpe':
					case '.png':
					case '.gif':
						isImage = true;
				}

				var event = $.Event('AttachmentQueueValidation');
				event.file = file;
				event.flow = flow;
				event.isImage = isImage;
				$container.trigger(event);

				if (event.isDefaultPrevented())
				{
					return false;
				}

				if (maxFileSize > 0 && file.size > maxFileSize && !isImage) // allow web images to bypass the file size check, as they (may) be resized on the server
				{
					attachmentErrorAlert(file, ERRORS.TOO_LARGE);
					return false;
				}

				event = $.Event('AttachmentQueued');
				event.file = file;
				event.flow = flow;
				event.isImage = isImage;
				$container.trigger(event);
			});
			flow.on('filesSubmitted', function() { flow.upload(); });
			flow.on('fileProgress', function(file)
			{
				// for improved swfupload compat
				file.id = file.uniqueIdentifier;

				$container.trigger(
					{
						type: 'AttachmentUploadProgress',
						file: file,
						bytes: Math.round(file.progress() * file.size),
						flow: flow
					});
			});
			flow.on('fileSuccess', function(file, message)
			{
				try
				{
					if (useFusty && message.substr(0, 1) == '<')
					{
						message = $($.parseHTML(message)).text();
					}

					var ajaxData = $.parseJSON(message);
				}
				catch (e)
				{
					console.warn(e);
					return;
				}

				// for improved swfupload compat
				file.id = file.uniqueIdentifier;

				if (ajaxData.error)
				{
					$container.trigger({
						type: 'AttachmentUploadError',
						file: file,
						ajaxData: ajaxData,
						flow: flow
					});
				}
				else
				{
					$container.trigger({
						type: 'AttachmentUploaded',
						file: file,
						ajaxData: ajaxData,
						flow: flow
					});
				}
			});
			flow.on('fileError', function(file, message)
			{
				var errorCode = 0;

				// for improved swfupload compat
				file.id = file.uniqueIdentifier;

				$container.trigger({
					type: 'AttachmentUploadError',
					file: file,
					errorCode: errorCode,
					message: message,
					ajaxData: { error: [ $container.data('err-unknown') ] },
					flow: flow
				});
			});
		}
		else
		{
			console.error('flow.js must be loaded');
		}

		/**
		 * Bind to the AutoInlineUploadEvent of the document, just in case SWFUpload failed
		 */
		$(document).bind('AutoInlineUploadComplete', function(e)
		{
			if (uniqueKey && e.ajaxData && uniqueKey !== e.ajaxData.key)
			{
				return false;
			}

			var $target = $(e.target);

			if ($target.is('form.AttachmentUploadForm'))
			{
				if ($trigger.overlay())
				{
					$trigger.overlay().close();
				}

				$container.trigger(
				{
					type: 'AttachmentUploaded',
					ajaxData: e.ajaxData
				});

				return false;
			}
		});

		return {
			getSwfUploader: function()
			{
				return null;
			},
			getFlowUploader: function()
			{
				return flow;
			},
			swfAlert: attachmentErrorAlert,
			attachmentErrorAlert: attachmentErrorAlert
		};
	};

	// *********************************************************************

	XenForo.AttachmentEditor = function($editor)
	{
		this.setVisibility = function(instant)
		{
			var $hideElement = $editor.closest('.ctrlUnit'),
				$insertAll = $editor.find('.AttachmentInsertAllBlock'),
				$files = $editor.find('.AttachedFile:not(#AttachedFileTemplate)'),
				$attachedFilesUnit = $files.parents('.AttachedFilesUnit'),
				$submit = $attachedFilesUnit.find('.ctrlUnit.submitUnit .button'),
				$processing = $files.filter('.Processing'),
				$images = $files.filter('.AttachedImage');

			console.log('Attachments changed, total files: %d, images: %d', $files.length, $images.length);

			if ($hideElement.length == 0)
			{
				$hideElement = $editor;
			}

			if (instant === true)
			{
				if ($files.length)
				{
					if ($images.length > 1)
					{
						$insertAll.show();
					}
					else
					{
						$insertAll.hide();
					}

					$hideElement.show();
				}
				else
				{
					$hideElement.hide();
				}
			}
			else
			{
				if ($files.length)
				{
					if ($images.length > 1)
					{
						if ($hideElement.is(':hidden'))
						{
							$insertAll.show();
						}
						else
						{
							$insertAll.xfFadeDown(XenForo.speed.fast);
						}
					}
					else
					{
						if ($hideElement.is(':hidden'))
						{
							$insertAll.hide();
						}
						else
						{
							$insertAll.xfFadeUp(XenForo.speed.fast, false, XenForo.speed.fast, 'swing');
						}
					}

					$hideElement.xfFadeDown(XenForo.speed.normal);
				}
				else
				{
					$insertAll.slideUp(XenForo.speed.fast);

					$hideElement.xfFadeUp(XenForo.speed.normal, false, false, 'swing');
				}
			}

			if (!$processing.length)
			{
				$submit.prop('disabled', false).removeClass('disabled');
			}
		};

		this.setVisibility(true);

		$('#AttachmentUploader_' + $editor.data('uploadtype')).bind(
		{
			/**
			 * Fires when a file is added to the upload queue
			 *
			 * @param event Including e.file
			 */
			AttachmentQueued: function(e)
			{
				console.info('Queued file %s (%d bytes).', e.file.name, e.file.size);

				var $template = $('#AttachedFileTemplate').clone().attr('id', e.file.id);

				$template.find('.Filename').text(e.file.name);
				$template.find('.ProgressCounter').text('0%');
				$template.find('.ProgressGraphic span').css('width', '0%');

				if (e.isImage)
				{
					$template.addClass('AttachedImage');
				}

				$template.xfInsert('prependTo', '.AttachmentList_' + $editor.data('uploadtype') +'.New', null, insertSpeed);

				$template.find('.AttachmentCanceller').css('display', 'block').click(function()
				{
					if (e.swfUpload)
					{
						e.swfUpload.cancelUpload(e.file.id);
					}
					else if (e.file.flowObj)
					{
						e.file.cancel();
					}

					$template.xfRemove(null, function() {
						$editor.trigger('AttachmentsChanged');
					}, removeSpeed, 'swing');
				});

				var $unit = $template.parents('.AttachedFilesUnit'),
					$submit = $unit.find('.submitUnit .button');

				$submit.prop('disabled', true).addClass('disabled');

				$editor.trigger('AttachmentsChanged');
			},

			/**
			 * Fires when an upload progress update is received
			 *
			 * @param event Including e.file and e.bytes
			 */
			AttachmentUploadProgress: function(e)
			{
				console.log('Uploaded %d/%d bytes.', e.bytes, e.file.size);

				var percentNum = Math.min(100, Math.ceil(e.bytes * 100 / e.file.size)),
					percentage = percentNum + '%',
					$placeholder = $('#' + e.file.id),
					$counter = $placeholder.find('.ProgressCounter'),
					$graphic = $placeholder.find('.ProgressGraphic');

				$counter.text(percentage);
				$graphic.css('width', percentage);

				if (percentNum >= 100)
				{
					$placeholder.find('.AttachmentCanceller').prop('disabled', true).addClass('disabled');
				}

				if ($graphic.width() > $counter.outerWidth())
				{
					$counter.appendTo($graphic);
				}
			},

			/**
			 * Fires if an error occurs during the upload
			 *
			 * @param event
			 */
			AttachmentUploadError: function(e)
			{
				var error = '',
					file = e.file,
					id = file.uniqueIdentifier || file.id;

				$.each(e.ajaxData.error, function(i, errorText) { error += errorText + "\n"; });

				XenForo.alert(error + '<br /><br />' + XenForo.htmlspecialchars(file.name));

				var $attachment = $('#' + id),
					$editor = $attachment.closest('.AttachmentEditor');

				$attachment.xfRemove(null, function() {
					$editor.trigger('AttachmentsChanged');
				}, removeSpeed, 'swing');

				console.warn('AttachmentUploadError: %o', e);
			},

			/**
			 * Fires when a file has been successfully uploaded
			 *
			 * @param event
			 */
			AttachmentUploaded: function(e)
			{
				if (e.file) // SWFupload/flow.js method
				{
					var file = e.file,
						id = file.uniqueIdentifier || file.id,
						$attachment = $('#' + id),
						$attachmentText = $attachment.find('.AttachmentText'),
						$thumbnail = $attachment.find('.Thumbnail');

					new XenForo.ExtLoader(e.ajaxData, function()
					{
						$attachmentText.fadeOut(XenForo.speed.fast, function()
						{
							var $newAttachmentText = $(e.ajaxData.templateHtml).find('.AttachmentText');
							$newAttachmentText.xfInsert('insertBefore', $attachmentText, 'fadeIn', XenForo.speed.fast);

							$(e.ajaxData.templateHtml).find('.Thumbnail').xfInsert('replaceAll', $thumbnail, null, insertSpeed);

							$attachmentText.xfRemove();

							if ($newAttachmentText.find('.itemExtraInput.itemInputs').data('expanded'))
							{
								$newAttachmentText.find('a.ToggleTrigger.ItemToggleTrigger').click();
							}

							$attachment.attr('id', 'attachment' + e.ajaxData.attachment_id);
						});
					});
				}
				else // regular javascript method
				{
					var $attachment = $('#attachment' + e.ajaxData.attachment_id);

					if (!$attachment.length)
					{
						new XenForo.ExtLoader(e.ajaxData, function()
						{
							var $attachment = $(e.ajaxData.templateHtml),
								$attachmentText = $attachment.find('.AttachmentText');

							$attachment.xfInsert('prependTo', $editor.find('.AttachmentList_' + $editor.data('uploadtype') + '.New'), null, insertSpeed);

							if ($attachmentText.find('.itemExtraInput.itemInputs').data('expanded'))
							{
								$attachmentText.find('a.ToggleTrigger.ItemToggleTrigger').click();
							}
						});
					}
				}
				$attachment.removeClass('Processing');
				$editor.trigger('AttachmentsChanged');
			}
		});

		var thisVis = $.context(this, 'setVisibility');

		$('#QuickReply').bind('QuickReplyComplete', function(e)
		{
			$editor.find('.AttachmentList.New li:not(#AttachedFileTemplate)').xfRemove(null, thisVis);
		});

		$editor.bind('AttachmentsChanged', thisVis);
	};

	// *********************************************************************

	XenForo.AttachmentDeleter = function($trigger)
	{
		$trigger.css('display', 'inline-block').click(function(e)
		{
			var $trigger = $(e.target),
				href = $trigger.attr('href') || $trigger.data('href'),
				$attachment = $trigger.closest('.AttachedFile'),
				$thumb = $trigger.closest('.AttachedFile').find('.Thumbnail a'),
				attachmentId = $thumb.data('attachmentid');

			if (href)
			{
				$attachment.xfFadeUp(XenForo.speed.normal, null, removeSpeed, 'swing');

				XenForo.ajax(href, '', function(ajaxData, textStatus)
				{
					if (XenForo.hasResponseError(ajaxData))
					{
						$attachment.xfFadeDown(XenForo.speed.normal);
						return false;
					}

					var $editor = $attachment.closest('.AttachmentEditor');

					$attachment.xfRemove(null, function() {
						$editor.trigger('AttachmentsChanged');
					}, removeSpeed, 'swing');
				});

				if (attachmentId)
				{
					var editor = XenForo.getEditorInForm($trigger.closest('form'), ':not(.NoAttachment)');
					if (editor && editor.$editor)
					{
						editor.$editor.find('img[alt=attachFull' + attachmentId + '], img[alt=attachThumb' + attachmentId + ']').remove();
						var update = editor.$editor.data('xenForoElastic');
						if (update)
						{
							update();
						}
					}
				}

				return false;
			}

			console.warn('Unable to locate href for attachment deletion from %o', $trigger);
		});
	};

	// *********************************************************************

	XenForo.register('.AttachmentDownloader', 'XenForo.AttachmentDownloader');

	XenForo.register('.AttachmentUploader', 'XenForo.AttachmentUploader');

	XenForo.register('.AttachmentEditor', 'XenForo.AttachmentEditor');

	XenForo.register('.AttachmentDeleter', 'XenForo.AttachmentDeleter');

	XenForo.register('.SetAllTitlesOverlay', 'XenForo.SetAllTitlesForm');

	XenForo.register('.InsertFilename', 'XenForo.MediaGallery.InsertFilename');

	XenForo.register('a.SetAllImageTitles', 'XenForo.MediaGallery.SetAllImageTitles');
	XenForo.register('a.SetAllImageDescriptions', 'XenForo.MediaGallery.SetAllImageDescriptions');
	XenForo.register('a.SetAllVideoTitles', 'XenForo.MediaGallery.SetAllVideoTitles');
	XenForo.register('a.SetAllVideoDescriptions', 'XenForo.MediaGallery.SetAllVideoDescriptions');
}
(jQuery, this, document);