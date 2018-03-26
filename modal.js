/***************bootstrap modal helper class *************/
var Modal = function (options) {
    var $this = this;

    options = options ? options : {};
    $this.options = {};
    $this.options.header = options.header !== undefined ? options.header : false;
    $this.options.footer = options.footer !== undefined ? options.footer : false;
    $this.options.closeButton = options.closeButton !== undefined ? options.closeButton : true;
    $this.options.footerCloseButton = options.footerCloseButton !== undefined ? options.footerCloseButton : false;
    $this.options.id = options.id !== undefined ? options.id : "myModal";

	
    /**
     * Append modal window html to body
     */
    $this.createModal = function () {
        $('body').append('<div id="' + $this.options.id + '" class="modal fade"></div>');
        $($this.selector).append('<div class="modal-dialog custom-modal"><div class="modal-content"></div></div>');
        var win = $('.modal-content', $this.selector);
        if ($this.options.header) {
            win.append('<div class="modal-header"><h4 class="modal-title" lang="de"></h4></div>');
            if ($this.options.closeButton) {
                win.find('.modal-header').prepend('<button type="button" class="close" data-dismiss="modal">&times;</button>');
            }
        }

        win.append('<div class="modal-body"></div>');

        if ($this.options.footer) {
            win.append('<div class="modal-footer"></div>');
            if ($this.options.footerCloseButton) {
                win.find('.modal-footer').append('<a data-dismiss="modal" href="#" class="btn btn-default" lang="de">' + $this.options.footerCloseButton + '</a>');
            }
        }
		
		$($this.selector).on('hidden.bs.modal', function (e) {
			$($this.selector).remove();
		});
		
		
    };

    /**
     * Set header text. It makes sense only if the options.header is logical true.
     * @param {String} html New header text.
     */
    $this.setHeader = function (html) {
        $this.window.find('.modal-title').html(html);
    };

    /**
     * Set body HTML.
     * @param {String} html New body HTML
     */
    $this.setBody = function (html) {
        $this.window.find('.modal-body').html(html);
    };

    /**
     * Set footer HTML.
     * @param {String} html New footer HTML
     */
    $this.setFooter = function (html) {
        $this.window.find('.modal-footer').html(html);
    };

    /**
     * Return window body element.
     * @returns {jQuery} The body element
     */
    $this.getBody = function () {
        return $this.window.find('.modal-body');
    };

    /**
     * Show modal window
     */
    $this.show = function () {
        $this.window.modal('show');
    };

    /**
     * Hide modal window
     */
    $this.hide = function () {
        $this.window.modal('hide');
    };

    /**
     * Toggle modal window
     */
    $this.toggle = function () {
        $this.window.modal('toggle');
    };

    $this.selector = "#" + $this.options.id;
    if (!$($this.selector).length) {
        $this.createModal();
    }
	
    $this.window = $($this.selector);
    $this.setHeader($this.options.header);
};

/************** END HELPER CLASS ******************/
