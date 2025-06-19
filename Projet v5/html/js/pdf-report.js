document.getElementById('generate-pdf-btn').addEventListener('click', () => {
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF();

  const ipInput = document.getElementById('ip-input');
  const urlInput = document.getElementById('url-input');
  const resultsContainer = document.getElementById('results-output');

  const rawResults = resultsContainer?.innerText || 'No results available.';
  const date = new Date().toLocaleDateString('en-GB');
  const lines = pdf.splitTextToSize(rawResults, 180);

  // -------- Header
  let y = 20;
  pdf.setFontSize(18);
  pdf.text("Security Audit Report", 105, y, { align: 'center' });
  y += 10;

  pdf.setFontSize(12);
  pdf.setTextColor(90);
  pdf.text(`Generated on: ${date}`, 14, y);
  y += 6;
  y += 10;

  // -------- Confidentiality Notice
  pdf.setFontSize(10);
  pdf.setTextColor(150);
  pdf.text(
    "CONFIDENTIAL - This report contains sensitive information. Unauthorized distribution is prohibited.",
    14,
    y
  );
  y += 10;

  pdf.setDrawColor(160);
  pdf.line(14, y, 196, y);
  y += 10;

  // -------- Results Parsing
  pdf.setFontSize(12);
  pdf.setTextColor(0);

  let pageHeight = pdf.internal.pageSize.height;
  let margin = 20;

  let currentSection = '';
  lines.forEach((line) => {
    const sectionMatch = line.match(/^=== (.+) Results ===$/);
    if (sectionMatch) {
      y += 8; // Espace avant le nouveau bloc
      currentSection = sectionMatch[1];
      if (y > pageHeight - margin) {
        pdf.addPage();
        y = 20;
      }
      pdf.setFontSize(13);
      pdf.setTextColor(0, 51, 102);
      pdf.text(`${currentSection} Results`, 14, y);
      y += 6;
      pdf.setDrawColor(200);
      pdf.line(14, y, 196, y);
      y += 6;
      pdf.setFontSize(11);
      pdf.setTextColor(0);
    } else {
      if (y > pageHeight - margin) {
        pdf.addPage();
        y = 20;
      }
      pdf.text(line, 14, y);
      y += 5;
    }
  });

  // -------- Pagination
  const totalPages = pdf.internal.getNumberOfPages();
  for (let i = 1; i <= totalPages; i++) {
    pdf.setPage(i);
    pdf.setFontSize(10);
    pdf.setTextColor(160);
    pdf.text(`Page ${i} of ${totalPages}`, 105, pdf.internal.pageSize.height - 10, { align: 'center' });
  }

  // -------- Save
  const filename = `security_audit_${date.replace(/\//g, '-')}.pdf`;
  pdf.save(filename);
});
