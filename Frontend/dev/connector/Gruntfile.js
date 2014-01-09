module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      canvas: {
        src: 'src/connector.js',
        dest: '../../dist/js/connector.js'
      },
      connector_test: {
        src: 'src/connector-test.js',
        dest: '../../dist/js/connector-test.js'
      }
    },
    jsdoc: {
      dist : {
        src: ['src/connector.js'], 
        options: {
          destination: '../../dist/doc'
        }
      }
    },
    jshint: {
      all: ['Gruntfile.js', 'src/connector.js', 'src/connector-test.js']
    }
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  
  // Load the plugin that provides the "jsdoc" task
  grunt.loadNpmTasks('grunt-jsdoc');

  // Load the plugin that provides the "jshint" task
  grunt.loadNpmTasks('grunt-contrib-jshint');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

};
