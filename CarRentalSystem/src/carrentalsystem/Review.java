package carrentalsystem;

public class Review {
    private String reviewId, customerId, vehicleId, comment, reviewDate;
    private int rating;

    public Review() {}

    public Review(String reviewId, String customerId, String vehicleId, int rating, String comment, String reviewDate) {
        this.reviewId = reviewId; this.customerId = customerId; this.vehicleId = vehicleId;
        this.rating = rating; this.comment = comment; this.reviewDate = reviewDate;
    }

    public String getReviewId() { return reviewId; } public void setReviewId(String v) { reviewId = v; }
    public String getCustomerId() { return customerId; } public void setCustomerId(String v) { customerId = v; }
    public String getVehicleId() { return vehicleId; } public void setVehicleId(String v) { vehicleId = v; }
    public int getRating() { return rating; } public void setRating(int v) { if (v >= 1 && v <= 5) rating = v; }
    public String getComment() { return comment; } public void setComment(String v) { comment = v; }
    public String getReviewDate() { return reviewDate; } public void setReviewDate(String v) { reviewDate = v; }

    public String getStarDisplay() {
        StringBuilder sb = new StringBuilder();
        for (int i = 0; i < rating; i++) sb.append("*");
        for (int i = rating; i < 5; i++) sb.append("-");
        return sb.toString();
    }

    @Override
    public String toString() {
        return String.format("%-6s Vehicle:%-6s %s  %s  %s  %s", reviewId, vehicleId, getStarDisplay(), customerId, comment, reviewDate);
    }

    @Override public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || !(obj instanceof Review)) return false;
        return this.reviewId.equals(((Review) obj).reviewId);
    }
}
