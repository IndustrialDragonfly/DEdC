module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    uglify: {
      options: {
        banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
      },
      canvas: {
        src: 'src/canvas.js',
		dest: '../../dist/js/canvas.js'
      },
      canvas_test: {
        src: 'src/canvas-test.js',
		dest: '../../dist/js/canvas-test.js'
      }
    },
    jsdoc : {
        dist : {
            src: ['src/canvas.js'], 
            options: {
                destination: '../../dist/doc'
            }
        }
    }
  });

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-contrib-uglify');
  
  // Load the plugin that provides the "jsdoc" task
  grunt.loadNpmTasks('grunt-jsdoc');

  // Default task(s).
  grunt.registerTask('default', ['uglify']);

};
