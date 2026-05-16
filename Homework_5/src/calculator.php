<?php
$page_title = 'Калькулятор';
include __DIR__ . '/../templates/header.php';?>
<div class="calculator">
    <input type="number" id="calc-num1" placeholder="Первое число" />
    <select id="calc-operator">
        <option value="+">+</option>
        <option value="-">-</option>
        <option value="*">*</option>
        <option value="/">/</option>
    </select>
    <input type="number" id="calc-num2" placeholder="Второе число" />
    <input type="text" id="calc-result" placeholder="Результат" readonly />
    <div id="calc-operation" style="margin-top:5px;font-weight:bold;"></div>
</div>
<script>
    (function(){
        const n1 = document.getElementById('calc-num1');
        const n2 = document.getElementById('calc-num2');
        const op = document.getElementById('calc-operator');
        const res = document.getElementById('calc-result');
        const opText = document.getElementById('calc-operation');
        function calculate(){
            const a = parseFloat(n1.value);
            const b = parseFloat(n2.value);
            if(isNaN(a) || isNaN(b)){
                res.value = '';
                opText.textContent = '';
                return;
            }
            let r;
            switch(op.value){
                case '+': r = a + b; break;
                case '-': r = a - b; break;
                case '*': r = a * b; break;
                case '/': r = b !== 0 ? a / b : '∞'; break;
                default: r = '';
            }
            res.value = r;
            opText.textContent = `${a} ${op.value} ${b} = ${r}`;
        }
        n1.addEventListener('input', calculate);
        n2.addEventListener('input', calculate);
        op.addEventListener('change', calculate);
    })();
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>