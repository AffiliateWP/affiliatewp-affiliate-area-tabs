module.exports = function(grunt) {
    
        grunt.registerTask('watch', [ 'watch' ]);
    
        grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
    
            // Uglify.
            uglify: {
                options: {
                    mangle: false
                },
                js: {
                    files: {
                        'assets/js/admin-scripts.min.js': ['assets/js/admin-scripts.js']
                    }
                }
            },
    
            // LESS CSS.
            less: {
                style: {
                    files: {
                        "assets/css/admin.css": "assets/css/less/admin.less",
                    }
                },
                minify: {
                    options: {
                        compress: true
                    },
                    files: {
                        "assets/css/admin.min.css": "assets/css/less/admin.less",
                    }
                }
            },
    
            // Autoprefixer
            autoprefixer: {
                main: {
                    files:{
                        'assets/css/admin.css': 'assets/css/admin.css',
                        'assets/css/admin.min.css': 'assets/css/admin.min.css'
                    },
                },
            },

            // Watch our project for changes.
            watch: {
                // JS
                js: {
                    files: ['assets/js/admin-scripts.js'],
                    tasks: ['uglify:js'],
                },
                // CSS
                css: {
                    files: ['assets/css/less/**/*.less'],
                    tasks: ['less:style', 'less:minify', 'autoprefixer:main']
                },
            }
    
        });
    
        // Saves having to declare each dependency
        require( "matchdep" ).filterDev( "grunt-*" ).forEach( grunt.loadNpmTasks );
    
        grunt.registerTask('default', [ 'uglify', 'less', 'autoprefixer' ]);
        grunt.registerTask('js', [ 'uglify' ] );
    };
    