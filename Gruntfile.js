module.exports = function(grunt) {

  grunt.initConfig({
    uglify: {
      my_target: {
        files: {
          'assets/wp-simple-post-slider.min.js': ['src/wp-simple-post-slider.js']
        }
      }
    },
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'src',
          src: ['*.css'],
          dest: 'assets',
          ext: '.min.css'
        }]
      }
    },
    compress: {
      main: {
        options: {
          archive: 'wp-simple-post-slider.zip'
        },
        files: [
          {
            src: ['assets/**', 'vendor/**', 'templates/*', 'compilation_cache/*', 'languages/*', '*.php', 'tests'],
            dest: 'wp-simple-post-slider'
          } // includes files in path
        ]
      }
    },
    watch: {
      files: ['src/wp-simple-post-slider.js'],
      tasks: ['uglify']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['uglify', 'cssmin', 'compress']);

};