module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		watch: {
			css: {
				files: 'assets/css/main.scss',
				tasks: ['sass'],
				options: {
				livereload: true,
				},
			},
		},
        sass: {
            prod: {
				files: {
					'assets/css/style.css': ['assets/css/main.scss']
				}
            }
        },
	});
    
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', [
		'sass',
		'watch',
	]);
};