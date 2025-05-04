<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Faturas</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
</head>
<body>

<div class="container">
    <h1>Invoices management</h1>

    <form id="invoice-form">
        <fieldset>
            <legend></legend>

            <!-- Itens da Fatura - Alinhados Horizontalmente -->
            <div class="form-group">
                <label>Invoice Items</label>
                <div id="invoice-items">
                    <div class="item-row">
                        <input type="text" class="item-description" placeholder="Item description" required>
                        <input type="number" class="item-value" placeholder="Item value" required>
                        <button type="button" class="add-item-btn"><i class="fas fa-plus"></i> Adicionar</button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" id="create-invoice-btn">Criar Fatura</button>
            </div>
        </fieldset>

    </form>

    <div class="invoice-list">
        <h2>Invoice list</h2>
        <table id="invoice-table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Issue date</th>
                    <th>Paid Value</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="invoice-list"></tbody>
        </table>
        <p class="empty-message" id="empty-message">Nenhuma fatura cadastrada ainda.</p>
    </div>

    <!-- Modal Structure -->
    <div id="invoiceModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h4>Detalhes da Fatura</h4>
            <div id="invoiceDetailsCard">
                <!-- Content will be populated here -->
            </div>
            <!-- Hidden input field to store the invoiceId -->
            <input type="hidden" id="invoiceId" />
            <div class="modal-footer">
                <button class="btn green" id="pay-invoice-btn" onclick="openPaymentModal()">Pagar Fatura</button>
                <button class="btn red" onclick="closeModal()">Fechar</button>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closePaymentModal()">&times;</span>
            <h4>Pagamento da Fatura</h4>
            <div>
                <label for="payment-amount">Valor a Pagar</label>
                <input type="number" id="payment-amount" placeholder="Informe o valor" required>
            </div>
            <div class="modal-footer">
                <button class="btn green" onclick="submitPayment()">Confirmar Pagamento</button>
                <button class="btn red" onclick="closePaymentModal()">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/invoice.js') }}"></script>

</body>
</html>
