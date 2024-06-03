document.addEventListener("DOMContentLoaded", function() {
    equalizeCardHeight();
});

function equalizeCardHeight() {
    var cards = document.querySelectorAll(".card-equal-height");
    var maxHeight = 0;

    cards.forEach(function(card) {
        var cardBody = card.querySelector(".card-body");
        var cardHeight = cardBody.offsetHeight;

        if (cardHeight > maxHeight) {
            maxHeight = cardHeight;
        }
    });

    cards.forEach(function(card) {
        card.querySelector(".card-body").style.height = maxHeight + "px";
    });
}





//api dia chi
var citis = document.getElementById("province");
var districts = document.getElementById("district");
var wards = document.getElementById("ward");
var Parameter = {
    url: "https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json",
    method: "GET",
    responseType: "application/json",
};
var promise = axios(Parameter);
promise.then(function(result) {
    renderCity(result.data);
});

function renderCity(data) {
    for (const x of data) {
        citis.options[citis.options.length] = new Option(x.Name, x.Id);
    }
    citis.onchange = function() {
        district.length = 1;
        ward.length = 1;
        if (this.value != "") {
            const result = data.filter(n => n.Id === this.value);

            for (const k of result[0].Districts) {
                district.options[district.options.length] = new Option(k.Name, k.Id);
            }
        }
    };
    district.onchange = function() {
        ward.length = 1;
        const dataCity = data.filter((n) => n.Id === citis.value);
        if (this.value != "") {
            const dataWards = dataCity[0].Districts.filter(n => n.Id === this.value)[0].Wards;

            for (const w of dataWards) {
                wards.options[wards.options.length] = new Option(w.Name, w.Id);
            }
        }
    };
}
function previewImage(event) {
    const input = event.target;
    const previewContainer = document.getElementById('previewContainer');
    const preview = document.getElementById('preview');
    const deleteButton = document.getElementById('deleteButton');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            deleteButton.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage() {
    const preview = document.getElementById('preview');
    const uploadInput = document.getElementById('uploadInput');
    const deleteButton = document.getElementById('deleteButton');

    preview.style.display = 'none';
    uploadInput.value = ''; // Clear the file input
    deleteButton.style.display = 'none';
}



//tinh gia cuoc theo size
function handleInput(id, minValue) {
    var inputElement = document.getElementById(id);
    var inputValue = parseFloat(inputElement.value) || minValue;

    // Kiểm tra và giữ giá trị không nhỏ hơn giá trị tối thiểu
    if (inputValue < minValue) {
        inputValue = minValue;
    }

    // Cập nhật giá trị trong ô nhập liệu
    inputElement.value = inputValue;

    // Tính toán kết quả
    calculateResult();
}

function handleBlur(id, defaultValue) {
    var inputElement = document.getElementById(id);
    var inputValue = inputElement.value.trim();

    // Nếu ô nhập liệu trống, gán giá trị mặc định
    if (inputValue === '') {
        inputElement.value = defaultValue;
    }

    // Tính toán kết quả
    calculateResult();
}

function calculateResult() {
    // Tính toán kết quả
    var length = parseFloat(document.getElementById('length').value) || 1;
    var width = parseFloat(document.getElementById('width').value) || 1;
    var height = parseFloat(document.getElementById('height').value) || 1;
    var result = length * width * height *50;

    // Thêm "đ" vào cuối kết quả và định dạng giá trị tiền tệ
    var formattedResult = result.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });

    // Cập nhật giá trị trong ô kết quả
    document.getElementById('result').value = formattedResult;
}

// Gọi hàm calculateResult() khi trang web được tải để cập nhật giá trị mặc định ban đầu
calculateResult();
