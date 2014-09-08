# A sample Guardfile
# More info at https://github.com/guard/guard#readme

guard 'livereload' do
  # App
  watch(%r{app\/AppKernel.php$})
  watch(%r{app\/config\/.+\.yml$})

  # Models
  watch(%r{app\/models/(Repository\/){0,1}.+\.php$})

  # Public
  watch(%r{public\/[0-9a-z_\.\/]*$})

  # src
  watch(%r{src\/[a-zA-Z_\.\/]*$})

  # vendor
  watch(%r{vendor\/[a-zA-Z_\-\.\/]*$})
end
