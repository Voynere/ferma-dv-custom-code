<?php
/**
 * Q promocode admin list columns and countdown UI.
 *
 * @package Theme
 */

// Добавляем колонку "Осталось" в список промокодов.
add_filter(
	'manage_q_promocode_posts_columns',
	function( $columns ) {
		$new = array();

		// Вставим нашу колонку после заголовка (Title).
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;

			if ( 'title' === $key ) {
				$new['q_promo_time_left'] = 'Осталось';
			}
		}

		return $new;
	}
);

// Заполняем колонку "Осталось".
add_action(
	'manage_q_promocode_posts_custom_column',
	function( $column, $post_id ) {
		if ( 'q_promo_time_left' !== $column ) {
			return;
		}

		$lifetime_hours = (int) get_post_meta( $post_id, '_q_lifetime_hours', true );

		// Без срока действия.
		if ( $lifetime_hours <= 0 ) {
			echo '<span class="q-promo-no-expire">Без срока</span>';
			return;
		}

		// Время создания промо (UTC).
		$created_ts = get_post_time( 'U', true, $post_id );
		$expires_ts = $created_ts + $lifetime_hours * 3600;

		// Текущее время (UTC).
		$now  = current_time( 'timestamp', true );
		$diff = $expires_ts - $now;

		if ( $diff <= 0 ) {
			echo '<span class="q-promo-expired">Истёк</span>';
			return;
		}

		// Рисуем "заглушку" + передаём diff в секундах в data-атрибут.
		echo '<span
				class="q-promo-countdown q-promo-active"
				data-seconds-left="' . esc_attr( $diff ) . '"
			  ></span>';
	},
	10,
	2
);

// Динамический обратный отсчёт в списке q_promocode.
add_action(
	'admin_footer-edit.php',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'q_promocode' ) {
			return;
		}
		?>
		<style>
			.column-q_promo_time_left {
				width: 120px;
			}
			.q-promo-active {
				color: #2e7d32; /* зеленый */
				font-weight: 600;
			}
			.q-promo-expired {
				color: #b71c1c;
				font-weight: 600;
			}
			.q-promo-no-expire {
				color: #555;
			}
		</style>
		<script>
			(function () {
				function formatTime(seconds) {
					if (seconds <= 0) {
						return 'Истёк';
					}

					var hours = seconds / 3600;

					// Больше или равно часу — показываем в часах с одним знаком после запятой.
					if (hours >= 1) {
						var hStr = hours.toFixed(1).replace('.', ',');
						return hStr + ' ч';
					}

					// Меньше часа — показываем в минутах.
					var mins = Math.floor(seconds / 60);
					if (mins < 1) mins = 1;
					return mins + ' мин';
				}

				function tick() {
					var nodes = document.querySelectorAll('.q-promo-countdown[data-seconds-left]');
					nodes.forEach(function (el) {
						var sec = parseInt(el.getAttribute('data-seconds-left'), 10);
						if (isNaN(sec)) {
							return;
						}

						if (sec <= 0) {
							el.textContent = 'Истёк';
							el.classList.remove('q-promo-active');
							el.classList.add('q-promo-expired');
							el.removeAttribute('data-seconds-left');
							return;
						}

						el.textContent = formatTime(sec);
						el.setAttribute('data-seconds-left', sec - 1);
					});
				}

				document.addEventListener('DOMContentLoaded', function () {
					// Первичный рендер.
					tick();
					// Тикаем каждую секунду.
					setInterval(tick, 1000);
				});
			})();
		</script>
		<?php
	}
);

// Форматирование оставшегося времени: "2 д 3 ч", "5 ч 20 мин", "15 мин".
function q_promocode_format_time_left( int $seconds ): string {
	$days  = floor( $seconds / DAY_IN_SECONDS );
	$hours = floor( ( $seconds % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
	$mins  = floor( ( $seconds % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );

	$parts = array();

	if ( $days > 0 ) {
		$parts[] = $days . ' д';
	}
	if ( $hours > 0 ) {
		$parts[] = $hours . ' ч';
	}
	if ( $days === 0 && $mins > 0 ) {
		// минуты показываем только если дней нет (чтоб не раздувать строку).
		$parts[] = $mins . ' мин';
	}

	if ( empty( $parts ) ) {
		// На всякий случай, если осталось меньше минуты.
		return 'меньше 1 мин';
	}

	return implode( ' ', $parts );
}
