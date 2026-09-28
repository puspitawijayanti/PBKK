using System;
using System.Windows.Forms;
using System.Globalization;

namespace CalculatorApp
{
    public partial class Form1 : Form
    {
        // Langkah 3 — Menyiapkan Variabel
        double firstNumber = 0;
        double secondNumber = 0;
        double result = 0;
        string operation = "";

        public Form1()
        {
            InitializeComponent();
        }

        // Langkah 4 — Event Tombol Angka
        // Satu handler ini dipakai oleh SEMUA tombol angka (0-9)
        private void NumberButton_Click(object sender, EventArgs e)
        {
            Button button = (Button)sender;

            if (txtDisplay.Text == "0" || txtDisplay.Text == "")
                txtDisplay.Text = button.Text;
            else
                txtDisplay.Text += button.Text;
        }

        // Langkah 5 — Event Tombol Operator (+, -, x, /)
        private void OperatorButton_Click(object sender, EventArgs e)
        {
            Button button = (Button)sender;

            firstNumber = double.Parse(txtDisplay.Text);
            operation = button.Text;

            // Tampilkan ekspresi berjalan, contoh: "9 +"
            lblExpression.Text = txtDisplay.Text + " " + operation;

            txtDisplay.Clear();
        }

        // Langkah 6 — Menghitung Hasil dengan switch + try-catch
        private void btnEquals_Click(object sender, EventArgs e)
        {
            try
            {
                secondNumber = double.Parse(txtDisplay.Text);

                switch (operation)
                {
                    case "+":
                        result = firstNumber + secondNumber;
                        break;
                    case "-":
                        result = firstNumber - secondNumber;
                        break;
                    case "x":
                        result = firstNumber * secondNumber;
                        break;
                    case "/":
                        if (secondNumber == 0)
                            throw new DivideByZeroException("Tidak bisa membagi dengan nol.");
                        result = firstNumber / secondNumber;
                        break;
                }

                // Tampilkan ekspresi lengkap, contoh: "9 + 6 ="
                lblExpression.Text = firstNumber.ToString(CultureInfo.InvariantCulture) + " " + operation + " " +
                                      secondNumber.ToString(CultureInfo.InvariantCulture) + " =";

                txtDisplay.Text = result.ToString(CultureInfo.InvariantCulture);
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "Error");
                txtDisplay.Text = "0";
                lblExpression.Text = "";
            }
        }

        // Langkah 7 — Clear
        private void btnClear_Click(object sender, EventArgs e)
        {
            firstNumber = 0;
            secondNumber = 0;
            result = 0;
            operation = "";
            txtDisplay.Text = "0";
            lblExpression.Text = "";
        }

        // Langkah 7 — Decimal (mencegah titik dobel)
        private void btnDecimal_Click(object sender, EventArgs e)
        {
            if (!txtDisplay.Text.Contains("."))
                txtDisplay.Text += ".";
        }

        // ===== Tantangan Pengembangan =====

        // Tombol +/- : ubah tanda angka yang sedang tampil (positif <-> negatif)
        private void btnPlusMinus_Click(object sender, EventArgs e)
        {
            try
            {
                double current = double.Parse(txtDisplay.Text, CultureInfo.InvariantCulture);
                current *= -1;
                txtDisplay.Text = current.ToString(CultureInfo.InvariantCulture);
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "Error");
            }
        }

        // Tombol % : ubah angka yang tampil menjadi bentuk persen (dibagi 100)
        private void btnPercent_Click(object sender, EventArgs e)
        {
            try
            {
                double current = double.Parse(txtDisplay.Text, CultureInfo.InvariantCulture);
                current = current / 100;
                txtDisplay.Text = current.ToString(CultureInfo.InvariantCulture);
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "Error");
            }
        }

        // Tombol <- (Backspace) : hapus satu karakter terakhir dari display
        private void btnBackspace_Click(object sender, EventArgs e)
        {
            if (txtDisplay.Text.Length > 1)
            {
                txtDisplay.Text = txtDisplay.Text.Substring(0, txtDisplay.Text.Length - 1);
            }
            else
            {
                txtDisplay.Text = "0";
            }
        }
    }
}