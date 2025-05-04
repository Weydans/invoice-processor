// Função para adicionar um item de fatura
document
    .getElementById("invoice-items")
    .addEventListener("click", function (event) {
        if (event.target.classList.contains("add-item-btn")) {
            const itemRow = document.createElement("div");
            itemRow.classList.add("item-row");
            itemRow.innerHTML = `
                <input type="text" class="item-description" placeholder="Item description" required>
                <input type="number" class="item-value" placeholder="Item value" required>
                <button type="button" class="delete-item-btn"><i class="fas fa-trash"></i> Remove</button>
            `;
            document.getElementById("invoice-items").appendChild(itemRow);
        }
    });

// Função para excluir item
document
    .getElementById("invoice-items")
    .addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-item-btn")) {
            event.target.closest(".item-row").remove();
        }
    });

function clearForm() {
    document.getElementById("invoice-form").reset();
    document.querySelector("#invoice-items").innerHTML = `
            <div class="item-row">
                <input type="text" class="item-description" placeholder="Descrição do item" required>
                <input type="number" class="item-value" placeholder="Valor do item" required>
                <input type="number" class="item-paid-value" placeholder="Valor pago" required>
                <input type="number" class="item-payment-percentage" placeholder="Porcentagem paga" required>
                <button type="button" class="add-item-btn"><i class="fas fa-plus"></i> Adicionar</button>
            </div>
        `;
}

async function fetchInvoices() {
    try {
        const response = await fetch("/api/invoices", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        });

        if (response.ok) {
            const metadata = await response.json();
            const invoices = metadata.data;

            if (invoices.length > 0) {
                const invoiceList = document.getElementById("invoice-list");
                invoiceList.innerHTML = "";

                invoices.forEach((invoice) => {
                    const formattedDate = new Date(
                        invoice.issue_date
                    ).toLocaleDateString("pt-BR");
                    let status = resolveStatus(invoice.status);

                    const row = document.createElement("tr");

                    row.innerHTML = `
                            <td>${invoice.number}</td>
                            <td>${formattedDate}</td>
                            <td>R$ ${
                                invoice.amount_paid.replace(".", ",") || ""
                            }</td>
                            <td>${status}</td>
                            <td>
                                <button onclick="viewInvoiceDetails(${
                                    invoice.id
                                })">Detail</button>
                                ${
                                    invoice.status !== "paid" &&
                                    invoice.status !== "partial"
                                        ? `<button class="btn red" onclick="deleteInvoice(${invoice.id})">Remove</button>`
                                        : ""
                                }
                            </td>
                        `;

                    invoiceList.appendChild(row);
                });

                document.getElementById("empty-message").style.display =
                    invoices.length === 0 ? "block" : "none";
            } else {
                document.getElementById("empty-message").style.display =
                    "block";
            }
        } else {
            throw new Error("Erro ao buscar as faturas");
        }
    } catch (error) {
        console.error("Erro ao buscar faturas:", error);
    }
}

function resolveStatus(intStatus) {
    switch (intStatus) {
        case "1":
            return "Pending";
        case "2":
            return "Partially Paid";
        case "3":
            return "Paid";
            dafault: throw new Error("Inválid status!");
    }
}

async function viewInvoiceDetails(invoiceId) {
    document.getElementById("invoiceId").value = invoiceId;

    try {
        const response = await fetch(`/api/invoice/${invoiceId}`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || "Something went wrong!");
        }

        const invoice = await response.json();
        let initialValue = 0;
        let invoiceTotal = invoice.items.reduce((accumulator, item) => {
            let value = +item.value;
            return accumulator + value;
        }, initialValue);

        const invoiceDetailsCard =
            document.getElementById("invoiceDetailsCard");
        invoiceDetailsCard.innerHTML = `
                <div><strong>Invoice #: </strong>${invoice.number}</div>
                <div><strong>Issue date: </strong>${formatDate(
                    invoice.issue_date
                )}</div>
                <div><strong>Paid value: </strong> R$ ${
                    invoice.amount_paid ? invoice.amount_paid : "0,00"
                }</div>
                <div><strong>Total: </strong>R$ ${invoiceTotal.toFixed(2)}</div>
                <div><strong>Status: </strong>${resolveStatus(
                    invoice.status
                )}</div>

                <h5>Invoice items:</h5>
                <ul>
                    ${invoice.items
                        .map(
                            (item) => `
                        <li>
                            <strong>${item.description}</strong><br>
                            <b>Value:</b> ${item.value}<br>
                            <b>Percentage Paid:</b> ${item.percentage_paid}%<br>
                            <b>Amount Paid:</b> ${(
                                (item.value * item.percentage_paid) /
                                100
                            ).toFixed(2)}
                            ${
                                item.percentage_paid <= 0.0
                                    ? `<button class="btn red" onclick="deleteInvoiceItem(${item.id})">Remove</button>`
                                    : ""
                            }
                        </li>
                    `
                        )
                        .join("")}
                </ul>
            `;

        document.getElementById("invoiceModal").style.display = "block";
    } catch (error) {
        alert("Erro: " + error.message);
    }
}

async function deleteInvoiceItem(itemId) {
    try {
        const response = await fetch(`/api/invoice-item/${itemId}`, {
            method: "DELETE",
        });

        if (!response.ok) {
            let error = await response.json();
            throw new Error(error.message);
        }

        alert("Invoice item removed with success!");
        fetchInvoices();
        closeModal();
        viewInvoiceDetails(document.getElementById("invoiceId").value);
    } catch (error) {
        alert(error);
        console.error("Error:", error);
    }
}

function openPaymentModal() {
    document.getElementById("paymentModal").style.display = "block";
}

function closePaymentModal() {
    document.getElementById("paymentModal").style.display = "none";
}

async function submitPayment() {
    const paymentAmount = document.getElementById("payment-amount").value;
    const invoiceId = document.getElementById("invoiceId").value;

    // Validate the payment amount
    if (!paymentAmount || parseFloat(paymentAmount) <= 0) {
        alert("Por favor, informe um valor válido para o pagamento.");
        return;
    }

    if (!invoiceId) {
        alert("Erro: Nenhuma fatura selecionada.");
        return;
    }

    try {
        const response = await fetch(`/api/invoice/${invoiceId}/pay`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ amount: paymentAmount }),
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || "Erro ao processar o pagamento.");
        }

        alert(`Pagamento de R$ ${paymentAmount} realizado com sucesso!`);

        viewInvoiceDetails(invoiceId);
    } catch (error) {
        alert("Erro: " + error.message);
    }

    closePaymentModal();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function closeModal() {
    document.getElementById("invoiceModal").style.display = "none";
}

async function deleteInvoice(invoiceId) {
    try {
        const response = await fetch(`/api/invoice/${invoiceId}`, {
            method: "DELETE",
        });

        if (!response.ok) {
            let error = await response.json();
            throw new Error(error.message);
        }

        alert("Invoice removed with success!");
        fetchInvoices();
    } catch (error) {
        alert(error);
        console.error("Error:", error);
    }
}

window.onload = fetchInvoices;
