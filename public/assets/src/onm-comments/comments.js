jQuery(document).ready(function() {
  var alertZone = $('.alert');
  var body      = $('body');
  var postList  = $('.post-list');

  var commentFetchUrl = body.data('urlcommentsfetch');
  var commentVoteUrl  = body.data('urlcommentsvote');
  var params          = body.data();

  function readCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");

    // Loop through the array elements
    for (var i = 0; i < cookieArr.length; i++) {
      var cookiePair = cookieArr[i].split('=');

      /* Removing whitespace at the beginning of the cookie name
      and compare it with the given string */
      if (name === cookiePair[0].trim()) {
        // Decode the cookie value and return
        return decodeURIComponent(cookiePair[1]);
      }
    }

    // Return null if not found
    return null;
  }

  var onmuser = JSON.parse(readCookie('__onm_user'));

  // Show/Hide the auth section when focusing on the comment-form textarea
  $('.comment-form')
    .on('focus', '.textarea', function() {
      $('.auth').removeClass('hidden');
    })
    .on('blur', '.textarea', function() {
      if ($(this).val().length <= 0) {
        $('.auth').addClass('hidden');
      }
    });

  // Use AJAX to submit the form
  $('.comment-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);

    $.post(form.attr('action'), form.serialize())
      .done(function(answer) {
        alertZone
          .removeClass('alert-success')
          .removeClass('alert-error')
          .removeClass('alert-warning')
          .removeClass('alert-danger')
          .addClass('alert-' + answer.type)
          .html(answer.message)
          .slideDown()
          .fadeIn();

        form.find('input[type=text], input[type=email], textarea').val('');

        $('.auth').addClass('hidden');
      })
      .fail(function(data) {
        var answer = {};

        if (data.status > 499) {
          answer = {
            type: 'error',
            message: 'Error in the server'
          };
        } else {
          answer = JSON.parse(data.responseText);
        }

        alertZone
          .removeClass('alert-success')
          .removeClass('alert-error')
          .removeClass('alert-warning')
          .addClass('alert-danger')
          .html(answer.message)
          .slideDown()
          .fadeIn();
      }).always(function() {
        if (typeof grecaptcha !== 'undefined') {
          grecaptcha.reset();
        }
      });
  });

  if (onmuser.name.length > 0) {
    $('input[name=author-name]').attr('readonly', true);
    $('input[name=author-name]').val(onmuser.name);
  }

  if (onmuser.email.length > 0) {
    $('input[name=author-email]').attr('readonly', true);
    $('input[name=author-email]').val(onmuser.email);
  }

// Autoresize textarea while its being filled
  $('textarea').autosize({ append: '\n' });

  // Handle vote buttons
  $('.post-list').on('click', '.post.already-voted .vote', function(e) {
    e.preventDefault();
  });

  $('.post-list').on('click', '.post:not(.already-voted) .vote', function(e) {
    e.preventDefault();

    var voteButton   = $(this);
    var countElement = voteButton.find('span.count');
    var postElement  = voteButton.closest('.post');

    var vote                = voteButton.data('action');
    var id                  = postElement.data('id');
    var countElementContent = voteButton.find('.count').html();
    var currentVoteValue    = 0;

    if (countElementContent.length !== 0) {
      currentVoteValue = parseInt(countElementContent);
    }

    $.post(commentVoteUrl, {
      comment_id: id,
      vote: vote
    }).done(function() {
      postElement.addClass('already-voted');

      countElement.html(currentVoteValue + 1);

      if (vote === 'up') {
        voteButton.addClass('upvoted');
      } else {
        voteButton.addClass('downvoted');
      }
    });
  });

  // Handle the load more button loading
  $('.load-more').on('click', function(e) {
    e.preventDefault();

    var currentOffset = parseInt(params.offset);

    $.ajax({
      url: commentFetchUrl,
      dataType: 'json',
      data: {
        content_id: params.contentid,
        elems_per_page: params.elemsperpage,
        offset: currentOffset + 1,
      }
    }).done(function(data) {
      postList.append(data.contents);
      // Safeframe ad call
      var event = document.createEvent('Event');

      event.initEvent('OAM.load', true, true);
      document.dispatchEvent(event);

      body.data('offset', currentOffset + 1);

      if (!data.more) {
        $('.load-more').hide();
      }
    });
  });
});
