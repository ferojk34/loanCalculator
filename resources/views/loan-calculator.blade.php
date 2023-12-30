<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <title>Loan Calculator</title>
  <style>
    body {
      background-color: #f8f9fa;
    }

    #errorMessage {
            color: red;
            font-size: 16px;
            margin-bottom: 10px;
        }

    .container {
      background-color: #ffffff;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      padding: 30px;
      border-radius: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
    }

    #extraPaymentField {
      margin-top: 20px;
    }

    #loadingIndicator {
      display: none;
      text-align: center;
    }

    #responseTable {
      margin-top: 20px;
    }

    .error-message {
            color: red;
            font-size: 14px;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h2 class="text-center mb-4">Loan Calculator</h2>

  <div id="errorMessage"></div>
  <form id="loanForm">
    @csrf
    <div class="form-group">
      <label for="loanAmount">Loan Amount</label>
      <input type="number" class="form-control" id="loanAmount" name="loanAmount">
      <span class="error-message" id="loanAmountError"></span>
    </div>
    <div class="form-group">
      <label for="loanTerm">Loan Term (1-15 years)</label>
      <input type="number" class="form-control" id="loanTerm" name="loanTermInYears" min="1" max="20" required>
      <span class="error-message" id="loanTermError"></span>
    </div>
    <div class="form-group">
      <label for="interestRate">Interest Rate (1-100%)</label>
      <input type="number" class="form-control" id="interestRate" name="annualInterestRate" min="1" max="100" required>
      <span class="error-message" id="interestRateError"></span>
    </div>
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="extraPaymentCheckbox">
      <label class="form-check-label" for="extraPaymentCheckbox">Make extra payment (optional)</label>
    </div>
    <div class="form-group" id="extraPaymentField" style="display: none;">
      <label for="monthlyExtraPayment">Enter Your Monthly Extra Payment</label>
      <input type="number" class="form-control" id="monthlyExtraPayment" name="additionalPayment">
      <span class="error-message" id="additionalPaymentError"></span>
    </div>
    <div id="loadingIndicator">Loading...</div>
    <button type="submit" class="btn btn-primary mt-2">Submit</button>
  </form>

  <div id="repayment-amortization-schedule-section" class="mt-4" style="display: none">
    <h3>Loan Setup</h3>
    <h6>Loan Amount: <span id="repayment-loan-amount"></span></h6>
    <h6>Annual Interest Rate: <span id="repayment-interest-rate"></span> </h6>
    <h6>Loan Term: <span id="repayment-loan-term"></span> </h6>
    <h6>Repayment Amount : <span id="repayment-repayment-amount"></span> </h6>
    <h6>Effective Interest Rate: <span id="repayment-effective-interest-rate"></span> </h6>
    <div id="responseTable" class="mt-4">
      <table class="table">
        <thead>
          <th>Month</th>
          <th>Opening Balance</th>
          <th>Monthly Payment</th>
          <th>Interest Amount</th>
          <th>Principle Amount</th>
          <th>Extra Payment</th>
          <th>Remaining Loan Term</th>
          <th>Closing Balance</th>
        </thead>
        <tbody id="repayment-amortization-schedule">

        </tbody>
    </table>
    </div>
  </div>
  <div id="amortiation-schedule-section" class="mt-4" style="display: none">
    <h3>Loan Setup</h3>
    <h6>Loan Amount: <span id="schedule-loan-amount"></span></h6>
    <h6>Annual Interest Rate: <span id="schedule-annual-int-rate"></span> </h6>
    <h6>Loan Term: <span id="schedule-loan-term"></span> </h6>
    <div id="responseTable" class="mt-4">
      <table class="table">
        <thead>
          <th>Month</th>
          <th>Opening Balance</th>
          <th>Monthly Payment</th>
          <th>Interest Amount</th>
          <th>Principle Amount</th>
          <th>Closing Balance</th>
        </thead>
        <tbody id="amortization-schedule">

        </tbody>
    </table>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    // Toggle visibility of extra payment field

    $("#schedule-loan-amount").text("");
    $("#schedule-annual-int-rate").text("");
    $("#schedule-loan-term").text("");

    $('#extraPaymentCheckbox').change(function() {
      if ($(this).is(':checked')) {
        $('#extraPaymentField').show();
      } else {
        $('#extraPaymentField').hide();
        $('#monthlyExtraPayment').val('');
      }
    });

    // Handle form submission
    $('#loanForm').submit(function(event) {
      event.preventDefault();
      $('.error-message').text('');
      $('#errorMessage').text('');
      $("#repayment-amortization-schedule-section").hide();
      $("#amortiation-schedule-section").hide();
      $('#loadingIndicator').show();

      $.ajax({
        url: 'http://127.0.0.1:8000/api/public/loan/calculate-loan',
        method: 'POST',
        data: $(this).serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('#loadingIndicator').hide();

          displayResponse(response.payload);
          var loanAmountValue = $("#loanAmount").val();
          var annualInterestRate = $("#interestRate").val();
          var loanTerm = $("#loanTerm").val();
          var monthlyExtraPayment = $("#monthlyExtraPayment").val();

          $("#schedule-loan-amount").text(loanAmountValue);
          $("#schedule-annual-int-rate").text(annualInterestRate);
          $("#schedule-loan-term").text(loanTerm);
          $("#repayment_amount").text(monthlyExtraPayment);
          $("#effective_interest_rate").text(response.payload.effective_interest_rate);

          $("#repayment-loan-amountt").text(loanAmountValue);
          $("#repayment-interest-rate").text(annualInterestRate);
          $("#repayment-loan-term").text(loanTerm);
          $("#repayment-repayment-amount").text(monthlyExtraPayment);
          $("#repayment-effective-interest-rate").text(response.payload.effective_interest_rate);

        },
        error: function(error) {
          switch (error.status) {
            case 422:
              var errors = error.responseJSON.message;
              $.each(errors, function (field, errorMessage) {
                  $('#' + field + 'Error').text(errorMessage[0]);
              });
              break;
            default:
              $('#errorMessage').text('Something went wrong. Please try again later!');
          }
          $('#loadingIndicator').hide();
        }
      });
    });

    // Function to display response in table format
    function displayResponse(response) {

      // Iterate through each element in the array
      let html = '';
      response.schedules.forEach((payment, index) => {

        const {month, opening_balance, monthly_payment, interest_component, principal_component, closing_balance} = payment;
        html += `<tr>
          <td>${month}</td>
          <td>${opening_balance.toFixed(2)}</td>
          <td>${monthly_payment.toFixed(2)}</td>
          <td>${interest_component.toFixed(2)}</td>
          <td>${principal_component.toFixed(2)}</td>`;

          if (response.effective_interest_rate !== "") {
              html += `<td>${payment.extra_payment.toFixed(2)}</td>
                      <td>${payment.remaining_loan_term}</td>`;
          }

          html += `<td>${closing_balance.toFixed(2)}</td>
                </tr>`;
      });

      var scheduleTarget = response.effective_interest_rate !== ""
        ? "#repayment-amortization-schedule-section"
        : "#amortiation-schedule-section";
      $("#repayment-amortization-schedule, #amortization-schedule").html(html);
      $(scheduleTarget).show();

    }
  });
</script>

</body>
</html>
