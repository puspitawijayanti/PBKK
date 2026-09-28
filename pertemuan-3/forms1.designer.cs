namespace CalculatorApp
{
    partial class Form1
    {
        private System.ComponentModel.IContainer components = null;

        private System.Windows.Forms.Label lblTitle;
        private System.Windows.Forms.Label lblExpression;
        private System.Windows.Forms.TextBox txtDisplay;

        private System.Windows.Forms.Button btnPercent;
        private System.Windows.Forms.Button btnPlusMinus;
        private System.Windows.Forms.Button btnBackspace;
        private System.Windows.Forms.Button btnClear;

        private System.Windows.Forms.Button btn7;
        private System.Windows.Forms.Button btn8;
        private System.Windows.Forms.Button btn9;
        private System.Windows.Forms.Button btnDivide;

        private System.Windows.Forms.Button btn4;
        private System.Windows.Forms.Button btn5;
        private System.Windows.Forms.Button btn6;
        private System.Windows.Forms.Button btnMultiply;

        private System.Windows.Forms.Button btn1;
        private System.Windows.Forms.Button btn2;
        private System.Windows.Forms.Button btn3;
        private System.Windows.Forms.Button btnMinus;

        private System.Windows.Forms.Button btn0;
        private System.Windows.Forms.Button btnDecimal;
        private System.Windows.Forms.Button btnPlus;

        private System.Windows.Forms.Button btnEquals;

        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        private void InitializeComponent()
        {
            this.lblTitle = new System.Windows.Forms.Label();
            this.lblExpression = new System.Windows.Forms.Label();
            this.txtDisplay = new System.Windows.Forms.TextBox();

            this.btnPercent = new System.Windows.Forms.Button();
            this.btnPlusMinus = new System.Windows.Forms.Button();
            this.btnBackspace = new System.Windows.Forms.Button();
            this.btnClear = new System.Windows.Forms.Button();

            this.btn7 = new System.Windows.Forms.Button();
            this.btn8 = new System.Windows.Forms.Button();
            this.btn9 = new System.Windows.Forms.Button();
            this.btnDivide = new System.Windows.Forms.Button();

            this.btn4 = new System.Windows.Forms.Button();
            this.btn5 = new System.Windows.Forms.Button();
            this.btn6 = new System.Windows.Forms.Button();
            this.btnMultiply = new System.Windows.Forms.Button();

            this.btn1 = new System.Windows.Forms.Button();
            this.btn2 = new System.Windows.Forms.Button();
            this.btn3 = new System.Windows.Forms.Button();
            this.btnMinus = new System.Windows.Forms.Button();

            this.btn0 = new System.Windows.Forms.Button();
            this.btnDecimal = new System.Windows.Forms.Button();
            this.btnPlus = new System.Windows.Forms.Button();

            this.btnEquals = new System.Windows.Forms.Button();

            // ===== Form1 =====
            this.SuspendLayout();
            this.ClientSize = new System.Drawing.Size(360, 540);
            this.Text = "Calculator";
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;

            // ===== lblTitle =====
            this.lblTitle.Text = "Calculator";
            this.lblTitle.Font = new System.Drawing.Font("Segoe UI", 13F, System.Drawing.FontStyle.Bold);
            this.lblTitle.Location = new System.Drawing.Point(20, 10);
            this.lblTitle.Size = new System.Drawing.Size(320, 25);

            // ===== lblExpression (baris ekspresi, mis. "9 + 6") =====
            this.lblExpression.Text = "";
            this.lblExpression.Font = new System.Drawing.Font("Segoe UI", 10F);
            this.lblExpression.ForeColor = System.Drawing.Color.Gray;
            this.lblExpression.TextAlign = System.Drawing.ContentAlignment.MiddleRight;
            this.lblExpression.Location = new System.Drawing.Point(20, 38);
            this.lblExpression.Size = new System.Drawing.Size(320, 20);

            // ===== txtDisplay =====
            this.txtDisplay.Text = "0";
            this.txtDisplay.Font = new System.Drawing.Font("Segoe UI", 20F);
            this.txtDisplay.TextAlign = System.Windows.Forms.HorizontalAlignment.Right;
            this.txtDisplay.Location = new System.Drawing.Point(20, 60);
            this.txtDisplay.Size = new System.Drawing.Size(320, 40);
            this.txtDisplay.ReadOnly = true;

            // Helper layout values
            int startX = 20, startY = 110;
            int btnW = 70, btnH = 50, gap = 10;

            void Place(System.Windows.Forms.Button b, int col, int row, int colSpan = 1)
            {
                b.Location = new System.Drawing.Point(startX + col * (btnW + gap), startY + row * (btnH + gap));
                b.Size = new System.Drawing.Size(btnW * colSpan + gap * (colSpan - 1), btnH);
                b.Font = new System.Drawing.Font("Segoe UI", 12F);
            }

            // Row 0: %  ±  <-  C
            this.btnPercent.Text = "%"; Place(this.btnPercent, 0, 0);
            this.btnPlusMinus.Text = "+/-"; Place(this.btnPlusMinus, 1, 0);
            this.btnBackspace.Text = "<-"; Place(this.btnBackspace, 2, 0);
            this.btnClear.Text = "C"; Place(this.btnClear, 3, 0);

            // Row 1: 7 8 9 /
            this.btn7.Text = "7"; Place(this.btn7, 0, 1);
            this.btn8.Text = "8"; Place(this.btn8, 1, 1);
            this.btn9.Text = "9"; Place(this.btn9, 2, 1);
            this.btnDivide.Text = "/"; Place(this.btnDivide, 3, 1);

            // Row 2: 4 5 6 x
            this.btn4.Text = "4"; Place(this.btn4, 0, 2);
            this.btn5.Text = "5"; Place(this.btn5, 1, 2);
            this.btn6.Text = "6"; Place(this.btn6, 2, 2);
            this.btnMultiply.Text = "x"; Place(this.btnMultiply, 3, 2);

            // Row 3: 1 2 3 -
            this.btn1.Text = "1"; Place(this.btn1, 0, 3);
            this.btn2.Text = "2"; Place(this.btn2, 1, 3);
            this.btn3.Text = "3"; Place(this.btn3, 2, 3);
            this.btnMinus.Text = "-"; Place(this.btnMinus, 3, 3);

            // Row 4: 0 (span 2) . +
            this.btn0.Text = "0"; Place(this.btn0, 0, 4, 2);
            this.btnDecimal.Text = "."; Place(this.btnDecimal, 2, 4);
            this.btnPlus.Text = "+"; Place(this.btnPlus, 3, 4);

            // Row 5: = (lebar penuh)
            this.btnEquals.Text = "=";
            this.btnEquals.Location = new System.Drawing.Point(startX, startY + 5 * (btnH + gap));
            this.btnEquals.Size = new System.Drawing.Size(4 * btnW + 3 * gap, btnH);
            this.btnEquals.Font = new System.Drawing.Font("Segoe UI", 12F, System.Drawing.FontStyle.Bold);

            // ===== Event wiring =====
            this.btn0.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn1.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn2.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn3.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn4.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn5.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn6.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn7.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn8.Click += new System.EventHandler(this.NumberButton_Click);
            this.btn9.Click += new System.EventHandler(this.NumberButton_Click);

            this.btnPlus.Click += new System.EventHandler(this.OperatorButton_Click);
            this.btnMinus.Click += new System.EventHandler(this.OperatorButton_Click);
            this.btnMultiply.Click += new System.EventHandler(this.OperatorButton_Click);
            this.btnDivide.Click += new System.EventHandler(this.OperatorButton_Click);

            this.btnEquals.Click += new System.EventHandler(this.btnEquals_Click);
            this.btnClear.Click += new System.EventHandler(this.btnClear_Click);
            this.btnDecimal.Click += new System.EventHandler(this.btnDecimal_Click);

            this.btnPlusMinus.Click += new System.EventHandler(this.btnPlusMinus_Click);
            this.btnPercent.Click += new System.EventHandler(this.btnPercent_Click);
            this.btnBackspace.Click += new System.EventHandler(this.btnBackspace_Click);

            // ===== Tambahkan semua control ke Form =====
            this.Controls.Add(this.lblTitle);
            this.Controls.Add(this.lblExpression);
            this.Controls.Add(this.txtDisplay);
            this.Controls.Add(this.btnPercent);
            this.Controls.Add(this.btnPlusMinus);
            this.Controls.Add(this.btnBackspace);
            this.Controls.Add(this.btnClear);
            this.Controls.Add(this.btn7);
            this.Controls.Add(this.btn8);
            this.Controls.Add(this.btn9);
            this.Controls.Add(this.btnDivide);
            this.Controls.Add(this.btn4);
            this.Controls.Add(this.btn5);
            this.Controls.Add(this.btn6);
            this.Controls.Add(this.btnMultiply);
            this.Controls.Add(this.btn1);
            this.Controls.Add(this.btn2);
            this.Controls.Add(this.btn3);
            this.Controls.Add(this.btnMinus);
            this.Controls.Add(this.btn0);
            this.Controls.Add(this.btnDecimal);
            this.Controls.Add(this.btnPlus);
            this.Controls.Add(this.btnEquals);

            this.ResumeLayout(false);
            this.PerformLayout();
        }
    }
}