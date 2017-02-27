module.exports = function(grunt) {

  grunt.initConfig({
    gitinfo: {
      // options: {
      //   cwd: __dirname
      // },
      // local : {
      //   branch : {
      //     current : {
      //       SHA               : "Current HEAD SHA",
      //       shortSHA          : "Current HEAD short SHA",
      //       // name              : "Current branch name",
      //       currentUser       : "Current git user",
      //       lastCommitTime    : "Last commit time",
      //       lastCommitMessage : "Last commit message",
      //       lastCommitAuthor  : "Last commit author",
      //       lastCommitNumber  : "Last commit number"
      //     }
      //   }
      // }
    },
    // author: '<%= gitinfo.local.branch.current.lastCommitAuthor %>',
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
          archive: 'wp-simple-post-slider-{branch}.zip'
        },
        files: [
          {src: ['assets/**', 'assets/fonts/**', 'vendor/**', 'templates/*', 'src/*', 'compilation_cache/*', 'languages/*', '*.php', 'tests'], dest: 'wp-simple-post-slider'}, // includes files in path
        ]
      }
    },
    watch: {
      files: ['src/wp-simple-post-slider.js'],
      tasks: ['uglify']
    }
  });

  // console.log(grunt.config('author'));

  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-gitinfo');

  grunt.registerTask('get-branch', function () {
    var gitBanch = grunt.config('gitinfo.local.branch.current.name');
    var prettyBranches = {
      master: 'Branche principale',
      slide_images: 'Slide images'
    }
    grunt.config('prettyBranch', prettyBranches[gitBanch]);
    var archiveName = grunt.config('compress.main.options.archive');
    grunt.config('compress.main.options.archive', archiveName.replace('{branch}', gitBanch));
    grunt.log.ok();
  });

  grunt.registerTask('default', ['gitinfo', 'get-branch', 'uglify', 'cssmin', 'compress']);

};