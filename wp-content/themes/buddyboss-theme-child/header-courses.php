<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BuddyBoss_Theme
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>
        <script defer src="//unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
	</head>

	<body <?php body_class(); ?>>

        <?php wp_body_open(); ?>

		<?php if (!is_singular('llms_my_certificate')):
		 
			do_action( THEME_HOOK_PREFIX . 'before_page' ); 
	
		endif; ?>

		<div id="page" class="site">

			<?php do_action( THEME_HOOK_PREFIX . 'before_header' ); ?>

			<header id="masthead" >
                <div class="header-wrapper">
                    <div class="header-mycourses">
                        <svg width="35" height="32" viewBox="0 0 35 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 3.218h7.398l7.686 24.615c-.045.024-.147.06-.303.11-.158.052-.345.11-.559.178-.215.067-.44.14-.678.221l-.66.22c-.203.069-.372.12-.508.162-.135.039-.208.058-.22.058H0V3.218Zm27.104 0h6.958v25.564H21.891l-2.42-1.22 7.633-24.344Z" fill="#3A96DD"></path><path d="M19.469 25.125h2.42v3.655h-2.42a.721.721 0 0 1-.28.585 2.343 2.343 0 0 1-.676.373 4.286 4.286 0 0 1-.813.204c-.277.039-.5.058-.669.058-.18 0-.41-.017-.685-.05a3.567 3.567 0 0 1-.804-.195 2.22 2.22 0 0 1-.67-.382.748.748 0 0 1-.279-.594h-2.437v-3.655h2.437v-1.422c.022.226.13.412.321.56.193.144.416.264.67.355.253.088.513.151.777.186.266.032.49.05.67.05.18 0 .402-.017.669-.05.264-.035.524-.098.777-.186.254-.091.477-.21.67-.356a.78.78 0 0 0 .32-.559v1.422h.002Z" fill="#0063B1"></path><path d="M17.031 13.9a89.733 89.733 0 0 0 1.803-1.852 30.345 30.345 0 0 1 1.852-1.803c.531-.474 1.07-.936 1.62-1.38.545-.447 1.091-.902 1.633-1.364L30.422 2l2.421 2.438v23.125H18.249a1.19 1.19 0 0 0-.836.32c-.232.216-.36.48-.382.796a1.157 1.157 0 0 0-.382-.795 1.187 1.187 0 0 0-.836-.32H1.22V4.437L3.64 2l6.484 5.502c.542.461 1.088.916 1.634 1.363.549.444 1.088.906 1.619 1.38a30.372 30.372 0 0 1 1.852 1.803 91.584 91.584 0 0 0 1.803 1.853" fill="#CCC"></path><path d="M17.031 5.656c0-.507.096-.982.288-1.422a3.753 3.753 0 0 1 1.946-1.948c.44-.19.914-.285 1.421-.285h9.736v23.123c-1.477-.01-2.947-.02-4.402-.024a942.699 942.699 0 0 0-4.401-.009c-.553 0-1.095.056-1.625.169a4.362 4.362 0 0 0-1.43.568c-.422.264-.774.61-1.049 1.04-.277.429-.438.957-.483 1.59v-.541c0-.092-.03-.228-.094-.414a18.139 18.139 0 0 0-.523-1.355 34.525 34.525 0 0 1-.298-.728 16.949 16.949 0 0 1-.227-.618 1.37 1.37 0 0 1-.093-.408V5.656h1.235Z" fill="#F2F2F2"></path><path d="M17.031 5.656v22.922c-.022-.644-.167-1.187-.431-1.633a3.34 3.34 0 0 0-1.025-1.084 4.215 4.215 0 0 0-1.439-.591 7.837 7.837 0 0 0-1.692-.178c-1.478 0-2.947.002-4.4.009-1.457.004-2.926.013-4.404.024V2h9.736c.507 0 .981.095 1.421.286a3.755 3.755 0 0 1 1.946 1.948c.193.44.289.914.289 1.422" fill="#E5E5E5"></path></svg>
                        <span>My Courses</span>
                    </div>
                    <div class="header-right">
                        <div class="header-right__notif">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.208 0a2.633 2.633 0 1 0 0 5.265 2.633 2.633 0 0 0 0-5.265Zm0 3.75a1.116 1.116 0 1 1 0-2.233 1.116 1.116 0 0 1 0 2.232Z" fill="#FCD329"/><path d="M15.84 2.683a9.023 9.023 0 0 0-1.565-.383 1.116 1.116 0 1 1-2.123-.024 9.005 9.005 0 0 0-1.576.353v.004a2.633 2.633 0 0 0 2.633 2.632 2.622 2.622 0 0 0 2.631-2.582Z" fill="#FCD329" style="mix-blend-mode:multiply"/><path d="M15.84 2.683a9.023 9.023 0 0 0-1.565-.383 1.116 1.116 0 1 1-2.123-.024 9.005 9.005 0 0 0-1.576.353v.004a2.633 2.633 0 0 0 2.633 2.632 2.622 2.622 0 0 0 2.631-2.582Z" fill="#FCD329" style="mix-blend-mode:multiply"/><path d="M13.127 28a4.104 4.104 0 1 0 0-8.209 4.104 4.104 0 0 0 0 8.208Z" fill="#FCD329"/><path d="M23.953 19.17c-2.464-2.251-2.4-5.898-2.786-8.968-.982-7.8-8.04-7.569-8.04-7.569s-7.06-.23-8.042 7.57c-.386 3.07-.322 6.716-2.785 8.967C1.61 19.8.976 20.41 1 21.418c.032 1.255 1.054 2.077 2.2 2.35.39.093.794.127 1.193.127h17.468c.399 0 .803-.034 1.192-.127 1.147-.273 2.168-1.095 2.2-2.35.025-1.006-.61-1.619-1.3-2.248Z" fill="#FCD329"/><path d="M4.406 15.287c-.249 1.04-.624 2.026-1.232 2.888H23.08c-.608-.862-.984-1.85-1.232-2.888H4.406Z" fill="#FCD329" style="mix-blend-mode:multiply" opacity=".5"/><path d="M3.33 17.944h19.594c-.559-.87-.901-1.857-1.129-2.888H4.458c-.227 1.031-.569 2.017-1.128 2.888Z" fill="#F7A231"/><path d="M21.877 15.403c-.03-.116-.056-.23-.082-.347H4.458a14.46 14.46 0 0 1-.081.347h17.5Z" fill="#F7A231" style="mix-blend-mode:screen" opacity=".2"/><path d="M6.48 21.418c-.013-1.006.336-1.619.713-2.248 1.35-2.251 1.315-5.898 1.527-8.968.504-7.308 3.932-7.566 4.362-7.57-.533-.001-5.782.168-6.546 7.57-.317 3.07-.264 6.717-2.283 8.968-.565.63-1.086 1.241-1.065 2.248.025 1.255.863 2.077 1.802 2.35.32.093.65.127.978.127H8.34a1.62 1.62 0 0 1-.654-.127c-.628-.273-1.188-1.095-1.205-2.35Z" fill="#FCD329" style="mix-blend-mode:screen" opacity=".3"/><path d="M1.483 22.74c.412.516 1.043.867 1.717 1.028.39.093.793.127 1.192.127h17.469c.399 0 .803-.034 1.192-.127.675-.16 1.306-.512 1.717-1.028H1.483Z" fill="#FCD329" style="mix-blend-mode:multiply"/></svg>
                            <span>0</span>
                        </div>
                        <div class="header-right__coins">
                            <img src="<?=get_stylesheet_directory_uri();?>/assets/img/coin.png" alt="Coins">
                            <span>0</span>
                        </div>
                        <div class="header-right__user">
                            <div class="username">John Smith <svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m1 1 6 6 6-6" stroke="#37394A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            
                            <div class="flex justify-center">
                                <div
                                    x-data="{
                                        open: false,
                                        toggle() {
                                            if (this.open) {
                                                return this.close()
                                            }

                                            this.$refs.button.focus()

                                            this.open = true
                                        },
                                        close(focusAfter) {
                                            if (! this.open) return

                                            this.open = false

                                            focusAfter && focusAfter.focus()
                                        }
                                    }"
                                    x-on:keydown.escape.prevent.stop="close($refs.button)"
                                    x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                                    x-id="['dropdown-button']"
                                    class="relative"
                                >
                                    <!-- Button -->
                                    <button
                                        x-ref="button"
                                        x-on:click="toggle()"
                                        :aria-expanded="open"
                                        :aria-controls="$id('dropdown-button')"
                                        type="button"
                                        class="rounded-md bg-white px-5 py-2.5 shadow"
                                    >
                                        <span>Actions</span>
                                        <span aria-hidden="true">&darr;</span>
                                    </button>

                                    <!-- Panel -->
                                    <div
                                        x-ref="panel"
                                        x-show="open"
                                        x-transition.origin.top.left
                                        x-on:click.outside="close($refs.button)"
                                        :id="$id('dropdown-button')"
                                        style="display: none;"
                                        class="absolute left-0 mt-2 w-40 overflow-hidden rounded bg-white shadow-md"
                                    >
                                        <div>
                                            <a href="#" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50 disabled:text-gray-500" >
                                                Add task above
                                            </a>

                                            <a href="#" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50 disabled:text-gray-500" >
                                                Add task below
                                            </a>
                                        </div>

                                        <div class="border-t border-gray-200">
                                            <a href="#" class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50 disabled:text-gray-500" >
                                                Edit task
                                            </a>

                                            <a href="#" disabled class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                                Delete task
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo get_avatar( get_the_author_meta( 'ID' )); ?>
                        </div>
                    </div>
                </div>
			</header>

			<?php do_action( THEME_HOOK_PREFIX . 'after_header' ); ?>

			<?php do_action( THEME_HOOK_PREFIX . 'before_content' ); ?>

			<div id="content" class="site-content">

				<?php do_action( THEME_HOOK_PREFIX . 'begin_content' ); ?>

				<div class="container">
					<div class="<?php echo apply_filters( 'buddyboss_site_content_grid_class', 'bb-grid site-content-grid' ); ?>">