(() => {

const buyoneclick_buttons = document.querySelectorAll('.buyoneclick');

    const closeModalHandler = (e) => {
        const buyoneclick_modal = document.querySelector('.buyoneclick-modal');

        document.body.classList.remove('fixed');
        buyoneclick_modal.classList.remove('open');
		
		
    }

    const openModalHandler = (e) => {
		const buyoneclick_modal = document.querySelector('.buyoneclick-modal'),
			product_id_field = document.querySelector('.buyoneclick-modal__product-id');
		
		if(product_id_field) {
			const btn = e.target;
			console.log(btn);
			product_id_field.value = btn.dataset.id;
		}
		
		if(buyoneclick_modal) {
            const close_button = buyoneclick_modal.querySelector('.buyoneclick-modal__close');
			
			document.body.classList.add('fixed');
			buyoneclick_modal.classList.add('open');
			
			if(close_button) {
				close_button.addEventListener('click', closeModalHandler);
			}
		}
		
		const start_date = new Date();
		start_date.setHours(0,0,0,0);
		start_date.setDate(start_date.getDate() + 1);
		const end_date = new Date('2025-04-15');
		
		const buyoneclick_datepicker = new Datepicker('.buyoneclick-modal__time', {
			weekStart: 1,
			min: start_date,
			max: end_date
		});
    }

    if(buyoneclick_buttons) {
		buyoneclick_buttons.forEach((buyoneclick_button) => {
			buyoneclick_button.addEventListener('click', openModalHandler);
		});
    }	

})();

document.addEventListener('DOMContentLoaded', () => {
    const buyoneclick_delivery = document.querySelector('.buyoneclick-modal__delivery');
	if(buyoneclick_delivery) {
		buyoneclick_delivery.addEventListener('change',function() {
			const buyoneclick_shop = document.getElementById('buyoneclick-shop');
			if(buyoneclick_shop) {
				if(this.value == 2) {
					buyoneclick_shop.classList.remove('buyoneclick-modal__hide');
				} else {
					buyoneclick_shop.classList.add('buyoneclick-modal__hide');
				}
			}
		});
	}
	
	function onBuyOneClickFormSubmit(event) {
		event.preventDefault();
			
		const buyoneclick_form = event.target,
			buyoneclick_name = document.querySelector('.buyoneclick-modal__name'),
			buyoneclick_phone = document.querySelector('.buyoneclick-modal__phone'),
			buyoneclick_delivery = document.querySelector('.buyoneclick-modal__delivery'),
			buyoneclick_shop = document.querySelector('.buyoneclick-modal__shop'),
			buyoneclick_time = document.querySelector('.buyoneclick-modal__time'),
			buyoneclick_product = document.querySelector('.buyoneclick-modal__product-id'),
			buyoneclick_message = document.querySelector('.buyoneclick-modal__message'),
			buyoneclick_error = document.querySelector('.buyoneclick-modal__error'),
			buyoneclick_agree = document.querySelector('.buyoneclick-modal__checkbox');
			
		let requestData = {
			action: "buyoneclick_send",
			name: buyoneclick_name.value,
			phone: buyoneclick_phone.value,
			delivery: buyoneclick_delivery.value,
			time: buyoneclick_time.value,
			product_id: buyoneclick_product.value,
			agree: (buyoneclick_agree.checked) ? 1 : 0,
			nonce: buyoneclick_form.dataset.nonce,
		};
		
		if(buyoneclick_shop) {
			requestData.shop = buyoneclick_shop.value;
		}

		fetch('/wp-admin/admin-ajax.php', {
			method:"post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(requestData).toString(),
		}).then(function(response) {
			return response.json();
		}).then(function(response) {
			if(response.success) {
				buyoneclick_message.innerHTML = response.data.message;
				buyoneclick_message.style.display = "flex";
				buyoneclick_error.style.display = "none";
				buyoneclick_name.value = '';
				buyoneclick_phone.value = '';
				buyoneclick_time.value = '';
			} else {
				buyoneclick_error.innerHTML = response.data.error;
				buyoneclick_message.style.display = "none";
				buyoneclick_error.style.display = "flex";
			}
		});
			
		return false;
	}
	
	const buyoneclick_frm = document.querySelector(".buyoneclick-modal__form form");
								
	if(buyoneclick_frm) {
		buyoneclick_frm.addEventListener("submit", onBuyOneClickFormSubmit);
	}
});