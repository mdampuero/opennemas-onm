<script type="text/javascript">
  var devices = [ 'desktop', 'tablet', 'phone' ];

  for (var i = 0; i < devices.length; i++) {
    if (devices[i] === _onmaq.device) {
      var slots = document.getElementsByClassName('hidden-' + devices[i]);

      for (var j = 0; j < slots.length; j++) {
        slots[j].remove();
      }
    }
  }
</script>
