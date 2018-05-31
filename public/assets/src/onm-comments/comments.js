jQuery(document).ready(function() {
  var alertZone = $('.alert');
  var body      = $('body');
  var postList  = $('.post-list');

  var commentFetchUrl = body.data('urlcommentsfetch');
  var commentVoteUrl  = body.data('urlcommentsvote');
  var params          = body.data();

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
          .addClass('alert-' + answer.type)
          .html(answer.message)
          .slideDown()
          .fadeIn();

        form.find('input[type=text], input[type=email], textarea').val('');

        $('.auth').addClass('hidden');
      })
      .fail(function(data) {
        if (data.status > 499) {
          var answer = {
            type: 'error',
            message: 'Error in the server'
          };
        } else {
          var answer = JSON.parse(data.responseText);
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

  // Autoresize textarea while its being filled
  $('textarea').autosize({ append: "\n" });

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
      var currentVoteValue = parseInt(countElementContent);
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
      body.data('offset', currentOffset + 1);

      if (!data.more) {
        $('.load-more').hide();
      }
    });
  });

  $('[placeholder]').on('focus', function() {
    var input = $(this);

    if (input.val() === input.attr('placeholder')) {
      input.val('');
      input.removeClass('placeholder');
    }
  }).on('blur', function() {
    var input = $(this);

    if (input.val() === '' || input.val() === input.attr('placeholder')) {
      input.addClass('placeholder');
      input.val(input.attr('placeholder'));
    }
  });
});
