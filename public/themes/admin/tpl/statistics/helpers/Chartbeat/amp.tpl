<!-- Chartbeat tracking -->
<amp-analytics type="chartbeat">
  <script type="application/json">
    {
      "vars": {
        "uid": "' . $config['id'] . '",
        "domain": "' . $config['domain'] . '",
        "sections": "' . $smarty->tpl_vars['category_name'] . '",
        "authors": "' . $author . '"
      }
    }
  </script>
</amp-analytics>
<!-- End Chartbeat -->
