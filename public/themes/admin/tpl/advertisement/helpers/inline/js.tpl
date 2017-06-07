<script type="text/javascript">
  var adint = setInterval(function () {
    var slots = document.getElementsByClassName('hidden-' + _onmaq.device);

    if (slots.length === 0) {
      clearInterval(adint);
    }

    for (var j = 0; j < slots.length; j++) {
      slots[j].remove();
    }
  }, 250);
</script>
