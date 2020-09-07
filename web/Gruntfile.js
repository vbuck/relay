/**
 * PSA Assessment App – Frontend Grunt workflow
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */

module.exports = function (grunt) {
    require('jit-grunt')(grunt);
    let theme = grunt.option('theme') || 'default';
    console.log('==> Using theme: ' + theme);

    grunt.initConfig({
        less: {
            default: {
                options: {
                    compress: false,
                    optimization: 2,
                    sourceMap: true,
                    paths: [
                        'theme/' + theme + '/sources'
                    ]
                },
                files: {
                    'dist/main.css': 'theme/' + theme + '/sources/*.less'
                }
            }
        },
        watch: {
            styles: {
                files: ['theme/' + theme + '/sources/**/*.less'],
                tasks: ['less'],
                options: {
                    livereload: true,
                    nospawn: true
                }
            },
            scripts: {
                files: ['src/**/*.js', 'src/**/*.vue'],
                tasks: ['webpack']
            }
        },
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        cwd: 'theme/' + theme,
                        src: ['**/*', '!**/sources/**'],
                        dest: 'dist/'
                    }
                ]
            }
        },
        exec: {
            build: 'npx webpack --config webpack.config.js'
        }
    });

    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.registerTask('default', ['less', 'webpack']);
    grunt.registerTask('webpack', ['copy', 'exec']);
};
